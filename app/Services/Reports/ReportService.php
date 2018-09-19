<?php

namespace App\Services\Reports;


use App\Models\User;

class ReportService
{
    /**
     * @var FromQuery
     */
    protected $decorator;

    public function __construct(User $user, $nameReport)
    {
        $decorator = __NAMESPACE__ . '\\' . studly_case($user->roleName()) . '\\' . studly_case($nameReport);

        if (!class_exists($decorator)) {
            abort(404, "Report '$nameReport' not found or not available!");
        }

        $this->decorator = new $decorator($user);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function generate()
    {
        return $this->decorator->collection();
    }
}