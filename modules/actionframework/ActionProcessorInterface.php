<?php
interface ActionProcessorInterface {

    /**
     * Uses the provided action to select and process the provided payload
     *
     * @param   $action
     * @param   $payload
     * @return  array
     */
    public function process($action, $payload): array;

    /**
     * Returns the topic name associated with the with the ActionProcessor.
     *
     * @return  string
     */
    public function getTopic(): string;

}