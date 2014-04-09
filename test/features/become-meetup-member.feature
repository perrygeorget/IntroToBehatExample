Feature:
  In order to register for the SF PHP Meetup group
  As a member of the developer community
  I need to be able to register to use Meetup.

  Scenario: I go to the group homepage and can join it.
    Given I am not signed in
    And I am on "http://www.meetup.com/sf-php/"
    When I follow "Join us"
    Then I should see "Meetup members, Log in"

  Scenario: I go to the group homepage and can sign in with FaceBook.
    Given I am not signed in
    And I am on "http://www.meetup.com/sf-php/"
    When I follow "Log in"
    Then I should see "Not registered with us yet? Sign up"