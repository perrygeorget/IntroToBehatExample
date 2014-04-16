<?php
/**
 * Steps definitions for sf-php meetup group home page.
 */

namespace Meetup;

use AbstractContext;
use Behat\Behat\Event\BaseScenarioEvent;
use Symfony\Component\Yaml\Yaml;

class GroupHome extends AbstractContext
{
    /**
     * @Then /^I should see upcoming and past events$/
     */
    public function iCanSeeEventsForTheGroup()
    {
        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        // Verify the existance of some things on the page that
        $mink->assertElementOnPage('#upcomingTab');
        $mink->assertElementOnPage('#pastTab');
    }

    /**
     * @Then /^I should be able to join the group$/
     */
    public function iShouldBeAbleToJoinTheGroup()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        $mink->clickLink('Join us!');
        $this->waitForPageToLoad();

        $mink->visit('http://www.meetup.com/sf-php/');

        $mink->assertPageContainsText('My profile');
    }

    /**
     * @Then /^I should be able to leave the group$/
     */
    public function iShouldBeAbleToLeaveTheGroup()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        $mink->clickLink('My profile');
        $this->waitForPageToLoad();

        $mink->clickLink('Leave group');
        $this->waitForPageToLoad();

        $mink->pressButton('Leave the Group');
        $this->waitForPageToLoad();

        $mink->assertPageContainsText("You've left");
    }

    /**
     * @BeforeScenario @required-member && @required-non-group-member
     */
    public function beforeScenarioEnsureUserIsNotAGroupMember(BaseScenarioEvent $event)
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');
        /** @var \Meetup\Authentication $authentication */
        $authentication = $main->getSubcontext('authentication');

        $authentication->beforeScenarioForRequiredMember($event);
        $authentication->iAmSignedIn();
        $mink->visit('http://www.meetup.com/sf-php/');

        $profileLinkElement = $page->findById('profile-link');
        if ($profileLinkElement) {
            $e = false;
            try {
                $this->iShouldBeAbleToLeaveTheGroup();
            } catch (\Exception $e) {
                // save it for later;
            }

            if ($e) throw $e;
            $mink->visit('http://www.meetup.com/sf-php/');
        }

        $mink->assertElementOnPage('.joinGroupButton');

        $authentication->iAmNotSignedIn();

        $this->printDebug('[BeforeScenarioEvent] The user is not a group member.');
    }
}