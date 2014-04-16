<?php
/**
 * Steps definitions for sf-php meetup group home page.
 */

namespace Meetup;

use Behat\MinkExtension\Context\RawMinkContext;

class GroupHome extends RawMinkContext
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

}