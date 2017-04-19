package com.journalpublication;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.builder.SpringApplicationBuilder;
import org.springframework.context.annotation.Bean;

@SpringBootApplication
public class JournalPubAppApplication {

	public static void main(String[] args) {
//		SpringApplication.run(JournalPubAppApplication.class, args);
        new SpringApplicationBuilder(JournalPubAppApplication.class)
	        .headless(false)
	        .web(false)
	        .run(args);		
	}
	
    @Bean
    public JournalPubFrame frame() {
        return new JournalPubFrame();
    }	
}
