<?php

namespace AppBundle\Repository;

/**
 * PhoneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PhoneRepository extends \Doctrine\ORM\EntityRepository
{

    /***
     * phone types accepted
     * @var array
     */
    public static $phoneTypes = ['Mobile', 'Home', 'Work', 'Fax', 'Other'];

    /***
     * Columns needed to manage in the controller for phones
     * @var array
     */
    public static $columns = ['type', 'countryCode', 'phone'];

}
