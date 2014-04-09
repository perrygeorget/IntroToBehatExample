Feature:
  In order to register for the SF PHP Meetup group
  As a member of the developer community
  I need to be able to find future events

  @required-member
  Scenario: I can sign in and see events.
    Given I am signed in
    And I am on "http://www.meetup.com/sf-php/"
    Then I can see events for the group

  Scenario: I can see events when signed out.
    Given I am not signed in
    And I am on "http://www.meetup.com/sf-php/"
    Then I can see events for the group
