<?php
/**
 * Step definitions for meetup authentication.
 */

namespace Meetup;

use AbstractContext;
use Behat\Behat\Event\StepEvent;
use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

class Authentication extends AbstractContext
{
    private $username;
    private $password;
    private $authenticationStateLoggedIn;

    /**
     * Signs the current signed in user out of meetup.com.
     *
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
     * Signs a user in to meetup.com.
     *
     * @Given /^(?:|I )am signed in$/
     * @When /^(?:I )sign in$/
     */
    public function iAmSignedIn()
    {
        if ($this->authenticationStateLoggedIn === true) {
            throw new \Exception('Can not sign in, because a user is already signed in.');
        }

        $main = $this->getMainContext();
        /** @var \Behat\MinkExtension\Context\MinkContext $mink */
        $mink = $main->getSubcontext('mink');

        // Go to the homepage, a known place.
        $mink->visit('http://www.meetup.com');

        // Sign in
        $mink->clickLink('Log in');
        $this->waitForPageToLoad();

        $mink->fillField('email', $this->username);
        $mink->fillField('password', $this->password);
        $mink->pressButton('Log in');

        $this->waitForPageToLoad();
    }

    /**
     * @BeforeStep @maintains-authentication-state
     */
    public function beforeStepObtainAuthenticationState(StepEvent $event)
    {
        // Don't bother if an exception has occurred.
        if ($event->hasException()) {
            return;
        }

        $session = $this->getSession();
        $page = $session->getPage();
        $element = $page->findById("nav-profile");
        $this->authenticationStateLoggedIn = $element && $element->isVisible();

        // Let the end user know what we did.  (This is mostly because this *is* a demo.)
        if ($this->authenticationStateLoggedIn) {
            $this->printDebug('[BeforeStepEvent] A user is signed in.');
        } else {
            $this->printDebug('[BeforeStepEvent] A user is not signed in.');
        }
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
        $this->printDebug("[BeforeScenarioEvent] Authentication configured for user \"{$this->username}\".");
    }

    /**
     * @BeforeScenario @logout-before-scenario
     * @AfterScenario @logout-after-scenario
     */
    public function afterScenarioEnsureLoggedOut()
    {
        if ($this->authenticationStateLoggedIn === false) {
            return;
        }

        $this->printDebug('[ScenarioEvent] Ensure that the user is logged out.');
        $this->iAmNotSignedIn();
    }
}