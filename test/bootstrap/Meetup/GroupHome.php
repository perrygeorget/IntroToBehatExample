<?php
/**
 * Created by IntelliJ IDEA.
 * User: george
 * Date: 4/6/14
 * Time: 7:29 PM
 */

namespace Meetup;

use Behat\Behat\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;

class GroupHome extends RawMinkContext {
    /**
     * @Then /^I can see events for the group$/
     */
    public function iCanSeeEventsForTheGroup()
    {
        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        $mink->assertElementOnPage('#upcomingTab');
        $mink->assertElementOnPage('#pastTab');
    }

}