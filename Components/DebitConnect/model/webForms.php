<?php


class webForms
{

    /** @var string */
    public $function;
    /** @var string */
    public $webForm;
    /** @var DateTime */
    public $dateTime;

    public function __construct($webForm,$function)
    {
        $this->setDateTime(new DateTime());
        $this->setFunction($function);
        $this->setWebForm($webForm);
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param string $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @return string
     */
    public function getWebForm()
    {
        return $this->webForm;
    }

    /**
     * @param string $webForm
     */
    public function setWebForm($webForm)
    {
        $this->webForm = $webForm;
    }

    /**
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }


}