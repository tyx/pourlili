Feature: Register new product in catalog
    To allow people to search in my product catalog
    As a baby registry owner
    I need to register new product

    Scenario: Register complete product
        When I register a new product named "Veilleuse" at price 39.00 described by "Pour faire de doux rêves" in my catalog
        Then my product named "Veilleuse" at price 39.00 described by "Pour faire de doux rêves" should be registered in my catalog
