Text
---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
/*    
    :Description:
        Created to show a filtered list of currencies by country to		
    :Database Target:
        fxDb6, fxDb6_Ops and fxDb6_Ops_RO
    :Revision History:
        2014-11-17	ab		Created to show a filtered list of countries to. Added order by Country
        2014-11-24	ab		Added date range validations. Remove commas from country names
		2014-11-24	BI	SCR 2163011  Added the a missing name of the column [fCountry], reformatted
		2015-10-30	ja		change order to match where agent sends to, internationalize country names
							Added OpenPayout for those countries where no corresp selection is required
		2016-08-02  KA	SCR 2719311 Changed the logic to display order and sorting for fxOnline
									Renamed the column DisplayOrder to match the old one
		2016-08-05  KA	SCR 2719311 Fixed issue with unicode for country name
		2016-08-09	EH	SCR 2719311 Removed AS DBO. Set common clauses.
		2016-09-14  KA  SCR 2768211 considered agent location setting
		2016-09-18  VC  Fix Do not display countries that appear in tier
		2016-09-19  VC  SCR 2768211 Add logic to reset tiers/displayorder if countrylevel/agent level defaults are not set
		2016-09-21  VC  SCR 2768211 Updated logic based on updated requirement
        2016-09-27  DB  SCR 2801511 Return OpenPayment info
    :Example Query:
		--Non fxOnline
		EXEC sales_sp_Services_Offered_CountriesTo_List 
				@fAppID = 10, @fAppObjectID=0, @lUserNameID=0, @fServiceID=111, @fDate = '10/30/2015',
				@fRecAgentID=11211, @fCountryFrom = 'US', @fRecAgentLocID=0, @bReturnOpenPaymentInfo = 1

		--fxOnline
		EXEC sales_sp_Services_Offered_CountriesTo_List 
				@fAppID = 500, @fAppObjectID=30000, @lUserNameID=0, @fServiceID=111, @fDate = '10/30/2015',
				@fRecAgentID=11311, @fCountryFrom = 'es', @fRecAgentLocID=0, @UserLocale = 'it-it'
*/  

CREATE PROCEDURE [dbo].[sales_sp_Services_Offered_CountriesTo_List]
(
    @fAppID INT,
    @fAppObjectID INT,
    @lUserNameID INT,
    
    @fServiceID INT = 111, -- Default to MT    
    @fDate DATE, 
    @fRecAgentID INT,
    @fCountryFrom CHAR(2) = '',
    @fRecAgentLocID INT = 0, 
	@UserLocale varchar(10) = '',
	@bReturnOpenPaymentInfo BIT = 0
)

AS

SET NOCOUNT ON

set @fRecAgentLocID = isnull(@fRecAgentLocID,0)

IF NOT (@fCountryFrom <> '')
BEGIN
	SELECT @fCountryFrom = fCountry 
	FROM contblAddresses WITH (NOLOCK) 
	WHERE fNameID = @fRecAgentID 
		AND fDisabled = 0   		
	ORDER BY fDefault DESC
END

-- create the initial list of countries from salestblservices_offered
CREATE TABLE #tCountriesServed 
(
	CountryID Int, Country CHAR(2), DisplayOrder Int, CountryName NVarchar(100) Null, OpenPayout bit
)

INSERT INTO #tCountriesServed (Country, DisplayOrder, OpenPayout)
SELECT DISTINCT fCountryTo, 100, 0
FROM salestblservices_offered WITH (NOLOCK) 
WHERE fServiceId = @fServiceID 
	AND  fExclude = 0 AND fDisabled = 0 AND fDelete = 0   
	AND (fDateBeg IS NULL OR fDateBeg <= @fDate)  
	AND (fDateEnd IS NULL OR fDateEnd <= '1900-01-01' OR fDateEnd >= @fDate)

-- get the list of orders restrictions by country only
CREATE TABLE #tRestrictions 
(
	  fCountry VARCHAR(2)
	, fItemID INT
	, fMessage VARCHAR(255)
	, bExclude BIT
)

INSERT INTO #tRestrictions
SELECT fcountryto, fItemID, fMessage, 0 
FROM mttblOrders_Restrictions WITH (NOLOCK) 
WHERE fCountryTo IN (SELECT Country FROM #tCountriesServed) 
	AND fCountryFrom IN ('', @fCountryFrom)
	AND fDisabled = 0 
	AND fDelete = 0
	AND fRecAgentID IN (0, @fRecAgentID) 
	AND fRecAgentLocID IN (0, @fRecAgentLocID)
	AND fTransferAmount = 0 
	AND fCorrespID = 0 
	AND fCorrespLocID = 0 
	AND fOrderEntryMethod = 0 
	AND fDelivMethod = 0 
	AND fProgramID = 0 
	AND fCurrencyFrom = '' 
	AND fCurrencyTo = '' 
	AND fAmountMax = 0 
	AND fAmountMin = 0 
	AND fStateFrom = '' 
	AND fStateTo= '' 
	AND fCityFrom= '' 
	AND fCityTo = ''
	AND (fDateBeg IS NULL OR fDateBeg <= @fDate)  
	AND (fDateEnd IS NULL OR fDateEnd <= '1900-01-01' OR fDateEnd >= @fDate)

-- check if there is any exclusion by agent
UPDATE #tRestrictions 
SET bExclude = 1 
FROM #tRestrictions r 
	INNER JOIN mttblOrders_Restrictions_exclude x WITH (NOLOCK) 
		ON r.fItemID = x.fRestrictionID   
WHERE x.fDisabled = 0 AND x.fDelete = 0
	AND x.fCountryFrom IN ('', @fCountryFrom) 
	AND (x.fRecAgentID IN (0, @fRecAgentID) 
	AND x.fRecAgentLocID IN (0, @fRecAgentLocID)) 
	AND x.fCountryTo IN (SELECT fCountry FROM #tCountriesServed)
	AND (fDateBeg IS NULL OR fDateBeg <= @fDate)  
	AND (fDateEnd IS NULL OR fDateEnd <= '1900-01-01' OR fDateEnd >= @fDate)
   
-- remove restricted countries
DELETE 
FROM  #tCountriesServed 
WHERE Country IN (SELECT fCountry FROM #tRestrictions WHERE bExclude = 0)

update c
Set DisplayOrder = p.fOrder
from #tCountriesServed c 
	inner join (Select fCountryTo, fOrder from oltblAgent_RateList_ToShow with (nolock)
Where fAgentID = @fRecAgentID and fDisabled = 0 and fDelete = 0) p
on c.Country = p.fCountryTo

if not @@rowcount > 0
Begin
	update c
	Set DisplayOrder = p.fOrder
	from #tCountriesServed c 
		inner join (Select fCountryTo, fOrder 
			from oltblAgent_RateList_ToShow_CountryDefault with (nolock)
	Where fCountryFrom = @fCountryFrom and fDisabled = 0 and fDelete = 0) p
	on c.Country = p.fCountryTo 
End

update z
set CountryID = c.fCountryID, CountryName = c.fCountry
from #tCountriesServed z 
	inner join systblRegCountries c with (nolock)
		on z.Country = c.fAbbrev 
Where c.fDisabled = 0 and c.fDelete = 0

delete 
from #tCountriesServed
Where not (len(isnull(Country, '')) = 2 and isnull(CountryID, 0) > 0)

if len(isnull(@UserLocale, '')) > 1
Begin
	update z
	set CountryName = c.fCountry
	from #tCountriesServed z 
		inner join systblRegCountries_Lang c with (nolock)
			on z.CountryID = c.fCountryID 
		inner join (Select fLanguageID from systblLanguages with (nolock) where fLocale = @UserLocale and fDisabled = 0 and fDelete = 0) l
			on c.fLanguageID = l.fLanguageID
	Where c.fDisabled = 0 and c.fDelete = 0
End

--Agent setting
update t
Set DisplayOrder = 0
from #tCountriesServed t 
	inner join (Select fSetting 
		from mttblRecAgent_Setting with (nolock)
		Where fNameID = @fRecAgentID and fNameIDLoc = 0 and fClassID = 2710 
		and fSettingID = 900 and fDisabled = 0 and fDelete = 0) s
on t.CountryID = s.fSetting

--RecAgent Loc Setting
if @fRecAgentLocID > 0
begin
	update t
	Set DisplayOrder = -1
	from #tCountriesServed t 
		inner join (Select fSetting 
			from mttblRecAgent_Setting with (nolock)
			Where fNameID = @fRecAgentID and fNameIDloc = @fRecAgentLocID and fClassID = 2710 and fSettingID = 900 and fDisabled = 0 and fDelete = 0) s
	on t.CountryID = s.fSetting
end

update t
Set OpenPayout = 1
from #tCountriesServed t 
	inner join systblSetting_Country s with (nolock) on t.CountryID = s.fCountryID
--Where s.fName like '%MTOpenPayout%'
Where s.fKey2 = 11453
and s.fVal = '1'

update #tCountriesServed
Set CountryName = REPLACE(CountryName, ',', '')
Where charindex(',', CountryName) > 0

create table #FinalList (fAbbrev char(2), fCountry nvarchar(255), fCountryID Int, DisplayOrder Int,
						IsOpenPayment Bit, fOPDefaultPayAgentID Int, fOPDefaultPayAgentLocID Int, 
						fOPDefaultCurrency Char(3), fSkipCityForOpenPayment Bit Default 0)

if @fAppID = 500 and @fAppObjectID = 30000
begin
	declare @lCountryFromID int 
	
	select @lCountryFromID = fCountryID 
	from systblRegCountries with (nolock) 
	where fAbbrev = @fCountryFrom and fDelete =0 and fDisabled = 0
	
	--Tier1 =Agent Loc setting or Agent setting or Country setting (11402)
	insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder)
	select Country, CountryName, CountryID, DisplayOrder
	from #tCountriesServed
	where DisplayOrder = -1  --Rec Agent Loc Level (setting)
	
	if not @@rowcount > 0
	begin
		insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder, IsOpenPayment)
		select Country, CountryName, CountryID, DisplayOrder, OpenPayout
		from #tCountriesServed
		where DisplayOrder = 0 --Rec Agent Level (setting)
		
		if not @@rowcount > 0
		begin
			insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder, IsOpenPayment)
			select c.fAbbrev, c.fCountry, c.fCountryID, -2, Null
			from systblSetting_Country s with (nolock) 
				inner join systblRegCountries c with (nolock) on s.fVal = c.fAbbrev 
			where s.fKey2 = 11402 and s.fCountryID = @lCountryFromID --Country Level setting (setting)
				and  s.fVal is not null
		end
	end
	
	--Tier2
	--from oltblAgent_RateList_ToShow & oltblAgent_RateList_ToShow_CountryDefault
	--Exclude the country from Tier1
	insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder, IsOpenPayment)
	select Country, CountryName, CountryID, DisplayOrder, OpenPayout
	from #tCountriesServed
	where DisplayOrder not in (-1, -2, 0, 100)
	and Country NOT in (select fAbbrev from #FinalList)
	
	--Tier 3: 
	--Everything else not in Tier 1 + Tier 2 list
	insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder, IsOpenPayment)
	select Country, CountryName, CountryID, 100, OpenPayout
	from #tCountriesServed 
	where Country NOT in (select fAbbrev from #FinalList)-- 2016-09-18 VC Fix
End
Else
begin
	insert into #FinalList (fAbbrev, fCountry, fCountryID, DisplayOrder, IsOpenPayment)
	select Country, CountryName, CountryID fCountryID, DisplayOrder, OpenPayout
	from #tCountriesServed	
end

If @bReturnOpenPaymentInfo = 1
Begin
	Update f
	Set fOPDefaultPayAgentID = l.fNameIDAgent, fOPDefaultPayAgentLocID = l.fNameIDLoc
	From #FinalList f Inner Join lttblPayAgentsLocs l with (nolock) on f.fAbbrev = l.fCountry
	Where f.IsOpenPayment = 1 And l.fLocType = 8 
	And l.fOnHold = 0 and l.fCannotPayOrders = 0 and l.fDelete = 0

	Update f
	Set fOPDefaultCurrency = c.fSymbol
	From #FinalList f Inner Join lttblPayAgentsCursLocs c with (nolock) on f.fOPDefaultPayAgentLocID = c.fNameIDLoc
	Where c.fDelete = 0 and f.fOPDefaultPayAgentLocID > 0


	-- temp mod for SkipForOpenPayment until rules added
	Update #FinalList Set fOPDefaultPayAgentID = 0 Where IsOpenPayment = 1 And fOPDefaultPayAgentID Is Null
	Update #FinalList Set fOPDefaultPayAgentLocID = 0 Where IsOpenPayment = 1 And fOPDefaultPayAgentLocID Is Null
	Update #FinalList Set fOPDefaultCurrency = '' Where IsOpenPayment = 1 And fOPDefaultCurrency Is Null
	
	Update #FinalList Set fSkipCityForOpenPayment = IsOpenPayment Where fOPDefaultPayAgentID > 0
	
	select fAbbrev, fCountry, fCountryID, IsOpenPayment, fOPDefaultPayAgentID, 
			fOPDefaultPayAgentLocID, fOPDefaultCurrency, fSkipCityForOpenPayment
	from #FinalList
	order by DisplayOrder, fCountry
End
Else
	select fAbbrev, fCountry, fCountryID
	from #FinalList
	order by DisplayOrder, fCountry



