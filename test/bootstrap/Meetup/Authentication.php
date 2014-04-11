<?php
/**
 * Created by IntelliJ IDEA.
 * User: george
 * Date: 4/6/14
 * Time: 7:29 PM
 */

namespace Meetup;

use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

class Authentication extends RawMinkContext
{
    private $username;
    private $password;

    /**
     * @Given /^(?:|I )am not signed in$/
     * @When /^(?:I )sign out$/
     */
    public function iAmNotSignedIn()
    {
        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        // I go to the logout page.
        $mink->visit('http://www.meetup.com/logout');
    }

    /**
     * @Given /^(?:|I )am signed in$/
     * @When /^(?:I )sign in$/
     */
    public function iAmSignedIn()
    {
        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        // Make sure we are logged out first.
        $this->iAmNotSignedIn();

        // Go to the homepage, a known place.
        $mink->visit('http://www.meetup.com');

        // Sign in
        $mink->clickLink('Log in');
        $mink->fillField('email', $this->username);
        $mink->fillField('password', $this->password);
        $mink->pressButton('Log in');
    }

    /**
     * @BeforeScenario @required-member
     */
    public function beforeScenarioForRequiredMember()
    {
        // Load the configuration file that contains the required data.
        // (The file is located at the top of the code base.)
        $yamlFile = __DIR__ . '/../../../configuration.yml';
        $config = Yaml::parse($yamlFile);

        if (!$config) {
            throw new InvalidConfigurationException(realpath($yamlFile) . ' not found.');
        }

        // Store the username and password to properties of the instance of this object.
        $this->username = $config['meetup']['registered-user']['username'];
        $this->password = $config['meetup']['registered-user']['password'];

        // Let the end user know what we did.  (This is mostly because this *is* a demo.)
        $this->printDebug("Authentication configured for user \"{$this->username}\".");
    }
}