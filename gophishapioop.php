<?php

namespace Drupal\gophish\Services;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\webprofiler\Config\ConfigFactoryWrapper;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

/**
 * Class GophishAPI.
 *
 * @package Drupal\gophish\Services
 */
class GophishAPI {
  /**
   * Drupal Guzzle client.
   *
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * Module's configuration settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  private $config;

  /**
   * Instance of logging service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerFactory;

  /**
   * Gophish host URL.
   *
   * @var string
   */
  private $host;

  /**
   * Gophish API key.
   *
   * @var string
   */
  private $apiKey;

  /**
   * Boolean for whether or not logging is enabled.
   *
   * @var bool
   */
  private $logging;

  /**
   * Gophish API constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   Core logging service.
   * @param \Drupal\webprofiler\Config\ConfigFactoryWrapper $config
   *   Access to Drupal configuration.
   * @param \GuzzleHttp\Client $client
   *   Access to Guzzle HTTP client.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, ConfigFactoryWrapper $config, Client $client) {
    $this->config = $config->get('gophish.config');
    $this->client = $client;
    $this->loggerFactory = $loggerFactory;

    $this->host = $this->config->get('host');
    $this->apiKey = $this->config->get('api_key');
    $this->logging = $this->config->get('logging');
  }

  /**
   * Gets a list of the sending profiles created by the authenticated user.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getSendingProfiles() {
    $response = $this->request('GET', '/api/smtp/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a sending profile given an ID.
   *
   * Returns a 404 error if no sending profile with the provided ID is found.
   *
   * @param int $id
   *   ID of the sending profile.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getSendingProfile($id) {
    $response = $this->request('GET', '/api/smtp/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates a sending profile.
   *
   * @param array $body
   *   The body of the request is an array representation of a sending profile.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createSendingProfile(array $body) {
    $response = $this->request('POST', '/api/smtp/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Modifies an existing sending profile.
   *
   * @param int $id
   *   ID of the sending profile.
   * @param array $body
   *   The body of the request is an array representation of a sending profile.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function modifySendingProfile($id, array $body) {
    $response = $this->request('PUT', '/api/smtp/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(
        ['id' => intval($id, 10)] + $body
      ),
    ]);

    return $response;
  }

  /**
   * Deletes a sending profile by ID.
   *
   * @param int $id
   *   ID of the sending profile.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteSendingProfile($id) {
    $response = $this->request('DELETE', '/api/smtp/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a list of templates.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getTemplates() {
    $response = $this->request('GET', '/api/templates/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a template with the provided ID.
   *
   * Returns a 404: Not Found error if the specified template doesn't exist.
   *
   * @param int $id
   *   ID of the template.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getTemplate($id) {
    $response = $this->request('GET', '/api/templates/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates a new template from the provided JSON request body.
   *
   * @param array $body
   *   The request body should be an array representation of a template.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createTemplate(array $body) {
    $response = $this->request('POST', '/api/templates/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Modifies an existing template.
   *
   * @param int $id
   *   ID of the template.
   * @param array $body
   *   The array representation of the template you wish to modify.
   *   The entire template must be provided, not just the fields you wish to
   *   update.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function modifyTemplate($id, array $body) {
    $response = $this->request('PUT', '/api/templates/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(
        ['id' => intval($id, 10)] + $body
      ),
    ]);

    return $response;
  }

  /**
   * Deletes a sending profile by ID.
   *
   * @param int $id
   *   ID of the template.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteTemplate($id) {
    $response = $this->request('DELETE', '/api/templates/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Imports an email as a template.
   *
   * @param bool $convert_links
   *   Whether or not to convert the links within the email to  automatically.
   * @param string $content
   *   The original email content in RFC 2045 format, including the original
   *   headers.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function importTemplate($convert_links, $content) {
    $response = $this->request('POST', '/api/import/email/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode([
        'convert_links' => $convert_links,
        'content' => $content,
      ]),
    ]);

    return $response;
  }

  /**
   * Returns a list of landing pages.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getLandingPages() {
    $response = $this->request('GET', '/api/pages/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a landing page given an ID.
   *
   * @param int $id
   *   ID of the landing page.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getLandingPage($id) {
    $response = $this->request('GET', '/api/pages/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates a landing page.
   *
   * @param array $body
   *   The array representation of the landing page to be created.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createLandingPage(array $body) {
    $response = $this->request('POST', '/api/pages/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Modifies an existing landing page.
   *
   * @param int $id
   *   ID of the landing page.
   * @param array $body
   *   The array representation of the landing page to be modified.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function modifyLandingPage($id, array $body) {
    $response = $this->request('PUT', '/api/pages/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(
        ['id' => intval($id, 10)] + $body
      ),
    ]);

    return $response;
  }

  /**
   * Deletes a landing page.
   *
   * @param int $id
   *   ID of the landing page.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteLandingPage($id) {
    $response = $this->request('DELETE', '/api/pages/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Fetches a URL to be later imported as a landing page.
   *
   * @param bool $include_resources
   *   Whether or not to create a <base> tag in the resulting HTML to resolve
   *   static references (recommended: false)
   * @param string $url
   *   The URL to fetch.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function importSite($include_resources, $url) {
    $response = $this->request('POST', '/api/import/site', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode([
        'include_resources' => $include_resources,
        'url' => $url,
      ]),
    ]);

    return $response;
  }

  /**
   * Returns a list of groups.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getGroups() {
    $response = $this->request('GET', '/api/groups/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a group with the given ID.
   *
   * @param int $id
   *   The group ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getGroup($id) {
    $response = $this->request('GET', '/api/groups/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a summary of each group.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getGroupsSummary() {
    $response = $this->request('GET', '/api/groups/summary', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a summary for a group.
   *
   * @param int $id
   *   The group ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getGroupSummary($id) {
    $response = $this->request('GET', '/api/groups/' . $id . '/summary', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates a new group.
   *
   * @param array $body
   *   The group to create in array format.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createGroup(array $body) {
    $response = $this->request('POST', '/api/groups/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Modifies a group.
   *
   * @param int $id
   *   The group ID.
   * @param array $body
   *   The group to create in array format.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function modifyGroup($id, array $body) {
    $response = $this->request('PUT', '/api/groups/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(
        ['id' => intval($id, 10)] + $body
      ),
    ]);

    return $response;
  }

  /**
   * Deletes a group.
   *
   * @param int $id
   *   The group ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteGroup($id) {
    $response = $this->request('DELETE', '/api/groups/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Reads and parses a CSV, returning data that can be used to create a group.
   *
   * @param object $file
   *   A file upload containing the CSV content to parse.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function importGroup($file) {
    $response = $this->request('POST', '/api/import/group/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(['file' => $file]),
    ]);

    return $response;
  }

  /**
   * Returns a list of campaigns.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getCampaigns() {
    $response = $this->request('GET', '/api/campaigns/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a campaign given an ID.
   *
   * @param int $id
   *   The campaign ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getCampaign($id) {
    $response = $this->request('GET', '/api/campaigns/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates and launches a new campaign.
   *
   * @param array $body
   *   The campaign details in array format.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createCampaign(array $body) {
    $response = $this->request('POST', '/api/campaigns/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Gets the results for a campaign.
   *
   * @param int $id
   *   The campaign ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getCampaignResults($id) {
    $response = $this->request('GET', '/api/campaigns/' . $id . '/results', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns summary information about a campaign.
   *
   * @param int $id
   *   The campaign ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getCampaignSummary($id) {
    $response = $this->request('GET', '/api/campaigns/' . $id . '/summary', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Deletes a campaign by ID.
   *
   * @param int $id
   *   The campaign ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteCampaign($id) {
    $response = $this->request('DELETE', '/api/campaigns/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Marks a campaign as complete.
   *
   * @param int $id
   *   The campaign ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function completeCampaign($id) {
    $response = $this->request('GET', '/api/campaigns/' . $id . '/complete', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a list of all user accounts in Gophish.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getUsers() {
    $response = $this->request('GET', '/api/users/', [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Returns a user with the given ID.
   *
   * @param int $id
   *   The user ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getUser($id) {
    $response = $this->request('GET', '/api/users/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Creates a new user.
   *
   * @param string $username
   *   The username for the account.
   * @param string $password
   *   The password to set for the account.
   * @param string $role
   *   The role slug to use for the account.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function createUser($username, $password, $role) {
    $response = $this->request('POST', '/api/users/', [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode([
        'role' => $role,
        'password' => $password,
        'username' => $username,
      ]),
    ]);

    return $response;
  }

  /**
   * Modifies a user account.
   *
   * This can be used to change the role, reset the password, or change
   * the username.
   *
   * @param int $id
   *   The user ID.
   * @param string|null $username
   *   The username for the account.
   * @param string|null $password
   *   The password to set for the account.
   * @param string $role
   *   The role slug to use for the account.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function modifyUser($id, $username, $password = NULL, $role = NULL) {
    if (is_null($password) && is_null($role)) {
      $body = [
        'username' => $username,
      ];
    }
    elseif (is_null($password) && !is_null($role)) {
      $body = [
        'username' => $username,
        'role' => $role,
      ];
    }
    elseif (!is_null($password) && is_null($role)) {
      $body = [
        'username' => $username,
        'password' => $password,
      ];
    }
    else {
      $body = [
        'username' => $username,
        'password' => $password,
        'role' => $role,
      ];
    }

    $response = $this->request('PUT', '/api/users/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode($body),
    ]);

    return $response;
  }

  /**
   * Deletes a user.
   *
   * Also deletes as every object (landing page, template, etc.) and
   * campaign they've created.
   *
   * @param int $id
   *   The user ID.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function deleteUser($id) {
    $response = $this->request('DELETE', '/api/users/' . $id, [
      'headers' => [
        'Authorization' => $this->apiKey,
      ],
    ]);

    return $response;
  }

  /**
   * Generic method for Guzzle requests.
   *
   * @param string $method
   *   The type of HTTP request.
   * @param string $endpoint
   *   The Gophish API endpoint.
   * @param array $options
   *   Options for the request.
   *
   * @return bool|\Symfony\Component\HttpFoundation\Response
   *   API call response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function request($method, $endpoint, array $options = []) {
    if (!isset($this->host) || !isset($this->apiKey)) {
      if ($this->logging == TRUE) {
        $this->loggerFactory->get('gophish')->error(
          'Gophish host or API key not set! Please check the module configuration settings.'
        );
      }

      return FALSE;
    }

    try {
      if ($this->logging == TRUE) {
        $request = $options;
        $request['headers']['Authorization'] = str_replace($this->apiKey, '[REDACTED API KEY]', $request['headers']['Authorization']);
        $this->loggerFactory->get('gophish')->info(
          'SENDING REQUEST <pre>Requested Endpoint: (' . $method . ') ' . $endpoint . '<br /><br />' .
          'Request Options: <br />' .
          print_r($request, TRUE) .
          '</pre>'
        );
      }

      $response = $this->client->request(
        $method,
        $this->host . $endpoint,
        $options
      );

      // Check if a JSON parsing error was encountered.
      $json = json_decode($response->getBody());
      if (json_last_error() !== JSON_ERROR_NONE) {
        $content = 'JSON Parsing Error: ' . print_r(json_last_error(), TRUE);
      }
      else {
        $content = print_r($json, TRUE);
      }

      if ($this->logging == TRUE) {
        $this->loggerFactory->get('gophish')->info(
          'REQUEST RESPONSE <pre>Requested Endpoint: (' . $method . ') ' . $endpoint . '<br /><br />' .
          'Status Code: ' . $response->getStatusCode() . '<br /><br />' .
          'Response Body: <br />' .
          $content .
          '</pre>'
        );
      }

      return new Response($response->getBody(), $response->getStatusCode());
    }
    catch (\Exception $e) {
      if ($this->logging == TRUE) {
        $this->loggerFactory->get('gophish')->info(
          'REQUEST ERROR <pre>Requested Endpoint: (' . $method . ') ' . $endpoint . '<br /><br />Status Code: ' . $e->getCode() . '<br /><br />' .
          'Error Message: ' . $e->getMessage() .
          '</pre>'
        );
      }

      return new Response($e, $e->getCode());
    }
  }

}
