<?php

class ActionProcessorMediator
{

    //private const ActionProcessorsIncludedKey = "ActionFrameworkProcessorsIncluded";
    private const ActionProcessorSuffix = "ActionProcessor.php";

    /**
    * @param    string      $topic      The component / domain that triggered the process
    * @param    string      $action     The reason the process is being triggered
    * @param                $payload
    * @return   array
    */
    public static function processAction(string $topic, string $action, $payload): array
    {
        //include all action processors
        ActionProcessorMediator::includeActionProcessors();

        //create class name from the topic
        $className = $topic . substr (self::ActionProcessorSuffix, 0, strpos(self::ActionProcessorSuffix,'.php'));

        //create a new instance of the class
        $actionProcessorInstance = new $className();

        //invoke the process method of action processor, if instance was created successfully
        if($actionProcessorInstance != null)
           return $actionProcessorInstance->process($action, $payload);

        return [false];
    }

    /**
     * Dynamically includes all actions processors in the action_processor directory
     */
    public static function includeActionProcessors()
    {
        //create an absolute path to the action processor directory
        $directory_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'action_processors';

        //create an instance of the directory iterator
        $actionProcessors = new DirectoryIterator($directory_path);

        foreach ($actionProcessors as $actionProcessor)
        {
            $fileName = $actionProcessor->getFilename();

            //ensure that you include action processor php files.
            if ($fileName != "." && $fileName != ".." && strrpos($fileName, self::ActionProcessorSuffix) != false)
            {
                //create an absolute path to the action processor file.
                $includePath = $directory_path . DIRECTORY_SEPARATOR . $fileName;

                //include file
                include ($includePath);
            }
        }

    }

}