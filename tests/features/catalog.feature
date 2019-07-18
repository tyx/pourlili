Feature: Register new product in catalog
    To allow people to search in my product catalog
    As a baby registry owner
    I need to register new product

    Scenario: Register complete product
    	Given I started a baby list
        When I register a new product named "Veilleuse" at price 39.00 described by "Pour faire de doux rêves" in my list
        And I register a new product named "Other" at price 39.00 described by "Pour faire de doux rêves" in my list
        Then my product named "Veilleuse" at price 39.00 described by "Pour faire de doux rêves" should be registered in my list
