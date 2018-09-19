<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Services\Reports\ReportService;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class ReportController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/client/reports/{name}",
     *     tags={"Client: reports"},
     *     summary="Display a reports",
     *     operationId="clientReportIndex",
     *     @SWG\Parameter(name="name", in="path", type="string", description="The name of the report",
     *          enum={"report_total_purchases", "report_goods", "report_customers"},
     *          default="report_total_purchases"),
     *     @SWG\Parameter(name="export", in="query", type="string", enum={"xls"},
     *          description="If you want to download a report, you should pass this parameter."),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="404", description="Report not found or not available."),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request, $name)
    {
        $this->validateReport($name);

        $user       = \Auth::user();
        $report     = new ReportService($user, $name);
        $collection = $report->generate();

        if ($request->has('export')) {
            $decorator = $this->createExportDecorator($name);

            if ($this->isValidDecorator($decorator)) {
                return \Excel::download(new $decorator($collection), $name . '.xls');
            }

            abort(404, "Report '$name' not available!");
        }

        return $this->sendCustomResponse($collection->toArray());
    }

    /**
     * @param string $name
     * @return string
     */
    protected function createExportDecorator(string $name)
    {
        return 'App\\Exports\\' . studly_case($name) . 'Export';
    }

    /**
     * Checks if the class has been defined
     *
     * @param string $decorator
     * @return bool
     */
    protected function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }

    /**
     * Checks if the report has been defined
     *
     * @param $name
     */
    protected function validateReport($name)
    {
        \Validator::make(['name'=>$name],
            ['name' => 'required|in:report_total_purchases,report_goods,report_customers'],
            ['name.in' => "Report $name does not exist!"]
        )->validate();
    }
}
