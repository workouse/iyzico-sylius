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
  Scenario: Failed payment: Success but cannot be cancelled, refund or post auth
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "5406670000000009"
    Then I should be notified that my payment has been failed "Local cards are invalid for foreign currency payments"

  @ui
  Scenario: Failed payment: Insufficient funds
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4111111111111129"
    Then I should be notified that my payment has been failed "Insufficient funds"

  @ui
  Scenario: Failed payment: Do not honour
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4129111111111111"
    Then I should be notified that my payment has been failed "Operation not approved"

  @ui
  Scenario: Failed payment: Invalid transaction
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4128111111111112"
    Then I should be notified that my payment has been failed "Invalid transaction"

  @ui
  Scenario: Failed payment: Lost card
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4127111111111113"
    Then I should be notified that my payment has been failed "Payment request has not passed Fraud check"

  @ui
  Scenario: Failed payment: Stolen card
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4126111111111114"
    Then I should be notified that my payment has been failed "Payment request has not passed Fraud check"

  @ui
  Scenario: Failed payment: Expired card
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4125111111111115"
    Then I should be notified that my payment has been failed "Expired card"

  @ui
  Scenario: Failed payment: Invalid cvc2
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4124111111111116"
    Then I should be notified that my payment has been failed "Invalid CVC2"

  @ui
  Scenario: Failed payment: Not permitted to card holder
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4123111111111117"
    Then I should be notified that my payment has been failed "Operation not permitted to card holder"

  @ui
  Scenario: Failed payment: Not permitted to terminal
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4122111111111118"
    Then I should be notified that my payment has been failed "Operation not permitted to terminal"

  @ui
  Scenario: Failed payment: Fraud suspect
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4121111111111119"
    Then I should be notified that my payment has been failed "Fraud suspect"

  @ui
  Scenario: Failed payment: Pickup card
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4120111111111110"
    Then I should be notified that my payment has been failed "Your card is restricted for online payments. You may enable online payments for your card by contacting your bank."

  @ui
  Scenario: Failed payment: General error
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4130111111111118"
    Then I should be notified that my payment has been failed "An error occurred while processing payment"

  @ui
  Scenario: Failed payment: General error
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Iyzico" payment method
    When I confirm my order with Iyzico payment
    And I sign in to Iyzico and pay fail "4130111111111118"
    Then I should be notified that my payment has been failed "An error occurred while processing payment"
