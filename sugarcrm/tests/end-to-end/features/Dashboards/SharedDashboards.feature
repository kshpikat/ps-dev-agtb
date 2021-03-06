# Your installation or use of this SugarCRM file is subject to the applicable
# terms available at
# http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
# If you do not agree to all of the applicable terms or do not have the
# authority to bind the entity as an authorized representative, then do not
# install or use this SugarCRM file.
#
# Copyright (C) SugarCRM Inc. All rights reserved.

@dashboard @dashlets @job6 @pr
Feature: Shareable Dashboards functionality verification

  Background:
    Given I use default account
    Given I launch App

  @dashboard
  Scenario Outline: Home > Shared dashboard

    # Create custom user
    Given I create custom user "user"
    And I open about view and login
    And I go to "Home" url

    # Create new dashboard > Save
    When I create new dashboard
      | *   | name            |
      | D_1 | <dashboardName> |

    # Add multiple dashlets to various columns of home dashboard
    When I add ActiveTasks dashlet to #Dashboard
      | label        |
      | Active Tasks |

    And I add KBArticles dashlet to #Dashboard
      | label       |
      | KB Articles |

    And I add ListView dashlet to #Dashboard
      | label       | module   | limit |
      | KB Articles | Contacts | 10    |

    And I add History dashlet to #Dashboard
      | label          |
      | Recent History |

    # Verify that new dashboard is created
    Then I verify fields on #Dashboard.HeaderView
      | fieldName | value           |
      | name      | <dashboardName> |

    # Share Dashboard
    When I go to "Dashboards" url
    And I filter for the Dashboards record *D_1 named "<dashboardName>"

    # Open record in the record view
    When I select *D_1 in #DashboardsList.ListView

    # Edit dashboard: 1. add user's team. 2. Make dashboard default
    When I click Edit button on #DashboardsRecord header
    And I provide input for #DashboardsRecord.RecordView view
      | team_name           | default_dashboard |
      | add: user userLName | true              |
    And I click Save button on #DashboardsRecord header
    And I close alert

    # Logout from Admin and Login as another user
    When I logout
    When I use account "user"
    When I open Dashboards view and login

    # Mark shared dashboard as favorite
    When I toggle favorite for *D_1 in #DashboardsList.ListView

    When I go to "Home" url

    # Verify the "Shared Dashboard" dashboard is shared successfully
    Then I verify fields on #Dashboard.HeaderView
      | fieldName | value           |
      | name      | <dashboardName> |

    Examples:
      | dashboardName    |
      | Shared Dashboard |
