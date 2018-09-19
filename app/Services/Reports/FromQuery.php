<?php
/**
 * Author: Vitalii Pervii
 * Author URI: https://www.amconsoft.com/
 * Date: 05.07.2018
 */

namespace App\Services\Reports;


use App\Models\User;
use Illuminate\Support\Collection;

interface FromQuery
{
    /**
     * FromQuery constructor.
     * @param User $user
     */
    public function __construct(User $user);

    /**
     * @return Collection
     */
    public function collection();
}