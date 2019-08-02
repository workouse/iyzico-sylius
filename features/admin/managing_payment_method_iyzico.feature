@managing_payment_method_iyzico
Feature: Adding a new payment method
  In order to pay for orders in different ways
  As an Administrator
  I want to add a new payment method to the registry

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui
  Scenario: Adding a new Iyzico payment method
    Given I want to create a new Iyzico payment method
    When I name it "Iyzico" in "English (United States)"
    And I specify its code as "iyzico_test"
    And I configure it with test Iyzico credentials
    And I add it
    Then I should be notified that it has been successfully created
    And the payment method "Iyzico" should appear in the registry
