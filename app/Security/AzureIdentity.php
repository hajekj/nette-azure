<?php
namespace App\Identity;

class Azure extends \Nette\Security\Identity
{
    private $data;
	/**
     * Data returned from oauth server
     *
     * @param array $data
     */
	public function __construct($data)
	{
		$this->data = $data;
        $roles = [];
        if(isset($data['roles'])) $roles = $data['roles'];
		parent::__construct($data['oid'], $roles, $data);
	}

	/**
     * {@inheritdoc}
     */
	public function getProvider()
	{
		return "Azure";
	}

    public function getData()
    {
        return $this->data;
    }
}
?>