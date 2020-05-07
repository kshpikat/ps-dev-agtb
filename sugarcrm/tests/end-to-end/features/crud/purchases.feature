# Your installation or use of this SugarCRM file is subject to the applicable
# terms available at
# http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
# If you do not agree to all of the applicable terms or do not have the
# authority to bind the entity as an authorized representative, then do not
# install or use this SugarCRM file.
#
# Copyright (C) SugarCRM Inc. All rights reserved.

@crud_modules_purchases @job1 @pr
Feature: Purchases module verification

  Background:
    Given I am logged in

  @user_profile
  Scenario: User Profile > Change license type
    When I choose Profile in the user actions menu
    # Change the value of License Type field
    When I change "LicenseTypes[]" enum-user-pref with "Sugar Sell" value in #UserProfile
    When I click on Save button on #UserProfile
    # Verify current value(s) of License Type field
    Then I verify value of "LicenseTypes[]" enum-user-pref field in #UserProfile
      | value                        |
      | Sugar Enterprise, Sugar Sell |
    When I click on Cancel button on #UserProfile


  @list
  Scenario: Purchases > List View > Preview
    Given Accounts records exist:
      | *   | name        |
      | A_1 | Account One |

    And Purchases records exist related via purchases link to *A_1:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |

    Then Purchases *Pur_1 should have the following values in the preview:
      | fieldName    | value                  |
      | name         | Purchase 1             |
      | account_name | Account One            |
      | service      | true                   |
      | renewable    | true                   |
      | description  | This is great purchase |


  @list-search
  Scenario Outline: Purchases > List View > Filter > Search main input
    Given 3 Purchases records exist:
      | *             | name               | service | renewable | description            |
      | Pur_{{index}} | Purchase {{index}} | true    | true      | This is great purchase |
    # Search for specific record
    When I choose Purchases in modules menu
    And I search for "Purchase <searchIndex>" in #PurchasesList.FilterView view
    # Verification if filtering is successful
    Then I should see [*Pur_<searchIndex>] on Purchases list view
    And I should not see [*Pur_1, *Pur_3] on Purchases list view
    Examples:
      | searchIndex |
      | 2           |

  @list-edit
  Scenario Outline: Purchases > List View > Inline Edit > Cancel/Save
    Given 2 Accounts records exist:
      | *           | name              |
      | A_{{index}} | Account_{{index}} |

    Given Purchases records exist related via purchases link to *A_1:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |

    # Edit (or cancel editing of) record in the list view
    When I <action> *Pur_1 record in Purchases list view with the following values:
      | fieldName    | value                  |
      | name         | Purchase <changeIndex> |
      | account_name | Account_<changeIndex>  |

    # Verify if edit (or cancel) is successful
    Then Purchases *Pur_1 should have the following values in the list view:
      | fieldName    | value                    |
      | name         | Purchase <expectedIndex> |
      | account_name | Account_<expectedIndex>  |

    Examples:
      | action            | changeIndex | expectedIndex |
      | edit              | 2           | 2             |
      | cancel editing of | 2           | 1             |


  @list-delete
  Scenario Outline: Purchases > List View > Delete > OK/Cancel
    Given Purchases records exist:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |
    # Delete (or Cancel deletion of) record from list view
    When I <action> *Pur_1 record in Purchases list view
    # Verify that record is (is not) deleted
    Then I <expected> see [*Pur_1] on Purchases list view
    Examples:
      | action             | expected   |
      | delete             | should not |
      | cancel deletion of | should     |

  @delete
  Scenario Outline: Purchases > Record View > Delete
    Given Purchases records exist:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |

    # Delete (or Cancel deletion of) record in the record view
    When I <action> *Pur_1 record in Purchases record view

    # Verify that record is (is not) deleted
    When I choose Purchases in modules menu
    Then I <expected> see [*Pur_1] on Purchases list view
    Examples:
      | action             | expected   |
      | delete             | should not |
      | cancel deletion of | should     |

  @copy
  Scenario Outline: Purchases > Record View > Copy > Save/Cancel
    Given Accounts records exist:
      | *   | name        |
      | A_1 | Account One |

    And Purchases records exist related via purchases link to *A_1:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |

    # Copy (or cancel copy of) record in the record view
    When I <action> *Pur_1 record in Purchases record view with the following header values:
      | *     | name                   |
      | Pur_2 | Purchase <changeIndex> |

    # Verify if copy is (is not) created
    Then Purchases *Pur_<expectedIndex> should have the following values:
      | fieldName    | value                    |
      | name         | Purchase <expectedIndex> |
      | account_name | Account One              |
      | service      | true                     |
      | renewable    | true                     |
      | description  | This is great purchase   |

    Examples:
      | action         | changeIndex | expectedIndex |
      | cancel copy of | 2           | 1             |
      | copy           | 2           | 2             |

  @create
  Scenario: Purchases > Create
    Given Accounts records exist:
      | *   | name        |
      | A_1 | Account One |

    Given ProductTemplates records exist:
      | *      | name        | discount_price | cost_price | list_price |
      | Prod_1 | Product One | 100            | 200        | 300        |

    # Click Create Purchase in Mega menu
    When I choose Purchases in modules menu and select "Create Purchase" menu item
    When I click show more button on #PurchasesDrawer view
    # Populate Header data
    When I provide input for #PurchasesDrawer.HeaderView view
      | *     | name            |
      | Pur_1 | My New Purchase |
    # Populate record data
    When I provide input for #PurchasesDrawer.RecordView view
      | *     | account_name | product_template_name | service | renewable | tag  | commentlog  | description                  |
      | Pur_1 | Account One  | Product One           | true    | true      | Alex | New Message | You've made a great purchase |
    # Save
    When I click Save button on #PurchasesDrawer header
    When I close alert
    # Verify that record is created successfully
    Then Purchases *Pur_1 should have the following values:
      | fieldName             | value                        |
      | name                  | My New Purchase              |
      | account_name          | Account One                  |
      | product_template_name | Product One                  |
      | service               | true                         |
      | renewable             | true                         |
      | tag                   | Alex                         |
      | description           | You've made a great purchase |


  @SS-431
  Scenario: Purchases > Calculate Purchase's Start Date and End Date fields
    Given Accounts records exist:
      | *   | name        |
      | A_1 | Account One |

    And Purchases records exist related via purchases link to *A_1:
      | *     | name       | service | renewable | description            |
      | Pur_1 | Purchase 1 | true    | true      | This is great purchase |

    And PurchasedLineItems records exist related via purchasedlineitems link to *Pur_1:
      | *     | name  | revenue | date_closed | quantity | service_start_date | service_end_date | service | renewable | discount_price |
      | PLI_1 | PLI_1 | 2000    | 2020-06-01  | 3.00     | 2020-06-01         | 2021-06-01       | true    | true      | 2000           |
      | PLI_2 | PLI_2 | 2000    | 2020-06-01  | 3.00     | 2020-05-30         | 2021-05-31       | true    | true      | 2000           |

    # Verify purchase start date and end date
    Then Purchases *Pur_1 should have the following values in the preview:
      | fieldName  | value      |
      | name       | Purchase 1 |
      | start_date | 05/30/2020 |
      | end_date   | 06/01/2021 |

    # Update PLI record
    When I choose PurchasedLineItems in modules menu
    When I select *PLI_1 in #PurchasedLineItemsList.ListView
    When I click Edit button on #PLI_1Record header
    When I click show more button on #PLI_1Record view
    When I provide input for #PLI_1Record.RecordView view
      | service_start_date |
      | 05/15/2020         |
    When I click Save button on #PLI_1Record header
    When I close alert

    # Update PLI record
    When I choose PurchasedLineItems in modules menu
    When I select *PLI_2 in #PurchasedLineItemsList.ListView
    When I click Edit button on #PLI_2Record header
    When I click show more button on #PLI_2Record view
    When I provide input for #PLI_2Record.RecordView view
      | service_start_date | service_duration_value | service_duration_unit |
      | 06/29/2020         | 1                      | Year(s)               |
    When I click Save button on #PLI_1Record header
    When I close alert

    # Verify purchase start date and end date
    Then Purchases *Pur_1 should have the following values in the preview:
      | fieldName  | value      |
      | name       | Purchase 1 |
      | start_date | 05/15/2020 |
      | end_date   | 06/28/2021 |


  @user_profile
  Scenario: User Profile > Change license type
    When I choose Profile in the user actions menu
    # Change the value of License Type field
    When I change "LicenseTypes[]" enum-user-pref with "Sugar Sell" value in #UserProfile
    When I click on Save button on #UserProfile
    # Verify current value(s) of License Type field
    Then I verify value of "LicenseTypes[]" enum-user-pref field in #UserProfile
      | value            |
      | Sugar Enterprise |
    When I click on Cancel button on #UserProfile
