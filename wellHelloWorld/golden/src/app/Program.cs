using System;
using static System.Console;
using Library;

class Program
{
    static void Main(string[] args)
    {
        WriteLine($"The answer is {new Thing().Get(19, 23)}");
    }
}
