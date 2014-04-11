Feature:
  In order to register for the SF PHP Meetup group
  As a member of the developer community
  I need to be able to find future events

  @required-member
  Scenario: I can sign in and see events.
    Given I am signed in
    When I go to "http://www.meetup.com/sf-php/"
    Then I should see upcoming and past events

  Scenario: I can see events when signed out.
    Given I am not signed in
    When I go to "http://www.meetup.com/sf-php/"
    Then I should see upcoming and past events
