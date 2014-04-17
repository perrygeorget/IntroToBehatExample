Feature:
  In order to register for the SF PHP Meetup group
  As a member of the developer community
  I need to be able to become a Meetup user

  Background:
    Given I am not signed in
    And I am on "http://www.meetup.com/sf-php/"

  Scenario: I go to the group homepage and can join it.
    When I follow "Join us"
    Then I should see "Meetup members, Log in"

  Scenario: I go to the group homepage and can sign in
    When I follow "Log in"
    Then I should see "Not registered with us yet? Sign up"

  @todo
  Scenario: I go to the group homepage and can sign in with FaceBook.
    When I follow "Log in"
    Then I login with Facebook