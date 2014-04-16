@required-member
Feature:
  In order to attend events for the SF PHP Meetup group
  As a member of the developer community
  I need to be able to join the SF PHP Meetup group

  @required-non-group-member
  Scenario: I can join the group
    Given I am signed in
    When I go to "http://www.meetup.com/sf-php/"
    Then I should be able to join the group

  Scenario: I can leave the group
    Given I am signed in
    When I go to "http://www.meetup.com/sf-php/"
    Then I should be able to leave the group
