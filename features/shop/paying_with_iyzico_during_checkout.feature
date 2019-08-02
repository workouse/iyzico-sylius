@paying_with_iyzico_for_order
Feature: Paying with Iyzico during checkout
  In order to buy products
  As a Customer
  I want to be able to pay with Iyzico

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "omer@eresbiotech.com" identified by "password123"
    And the store has a payment method "Iyzico" with a code "iyzico" and Iyzico Checkout gateway
    And the store classifies its products as "T-Shirts"
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "omer@eresbiotech.com"

  @ui
  Scenario: Successful payment
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay successfully
    Then I should be notified that my payment has been completed

  @ui
  Scenario: Failed payment: Not sufficient funds
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4111111111111129"
    Then I should be notified that my payment has been failed "Not sufficient funds"

  @ui
  Scenario: Failed payment: Do not honour
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4129111111111111"
    Then I should be notified that my payment has been failed "Do not honour"

  @ui
  Scenario: Failed payment: Invalid transaction
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4128111111111112"
    Then I should be notified that my payment has been failed "Invalid transaction"
