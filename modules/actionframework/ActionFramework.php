<?php

include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "Configs.php");
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "ActionProcessorInterface.php");
include(dirname(__FILE__) . DIRECTORY_SEPARATOR ."ActionProcessorMediator.php");

use Opis\Closure\SerializableClosure;


 class ActionFramework
{

     /**
     * Uses pre-defined functions to process the provided payload
     *
     * @param      string      $topic    The component / domain that triggered the process
     * @param      string      $action   The event / action being raised.
     * @param                  $payload
     * @return     array
     */
    public static function process(string $topic, string $action, $payload) : array
    {
        return ActionProcessorMediator::processAction($topic, $action, $payload);
    }

    /**
    * Adds the provided handler to the action's subscriptions.
    * Handler is required to return a bool value.
    *
    * @param    string      $action         The event / action being raised.
    * @param    string      $subscriber     An identifier of the subscriber / consumer.
    * @param    callable    $handler        A callback to be invoked when subscribed action / event is raised.
    */
    public static function subscribe(string $action, string $subscriber, callable $handler)
    {
        $subscriptions = self::getSubscriptions();

         //ensure that the action has subscriptions
        if (!isset($subscriptions[$action]))
        {
            $subscriptions[$action] = [];
        }

        //ensure that the subscriber has only one subscription per action
//        if(isset($subscriptions[$action][$subscriber]))
//        {
//            return;
//        }

        $wrapper = new SerializableClosure($handler);

        //add handler to the action's subscribers
        $subscriptions[$action][$subscriber] = $wrapper;

        // persist changes
         self::updateSubscriptions($subscriptions);
    }

     /**
      * Publishes the provided payload to the action's subscribers.
      *
      * @param string $action
      * @param $payload
      * @return bool
      */
    public static function publish(string $action, $payload): bool
    {

        $subscriptions = self::getSubscriptions();

        // only publish to actions that have subscriptions
        if(!isset($subscriptions[$action]))
        {
            return true;
        }

        $results = [];

        foreach ($subscriptions[$action] as $subscriber => $handler)
        {
            array_push($results, call_user_func($handler->getClosure(), $payload));
        }

        return !in_array(false, $results);
    }

     /**
      * Returns a collection of registered subscriptions.
      *
      */
    public static function getSubscriptions() : array
    {
        // check if there are any existing subscriptions
        if (!isset($_SESSION['subscriptions']))
        {
            $_SESSION['subscriptions'] = [];
        }

        return  $_SESSION['subscriptions'];
    }

     /**
     * Ensures that session subscriptions are updated with the new subscriptions
     *
     * @param   array       $subscriptions  A collection of the available action subscriptions.
     */
     public static function updateSubscriptions(array $subscriptions)
     {
        $_SESSION['subscriptions'] = $subscriptions;
     }

}
