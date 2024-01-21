<?php

namespace App\Repositories;


use App\Http\Resources\Api\Admins\Activity\ActivitiesResource;
use App\Http\Resources\Api\Admins\Amenties\AmentiesResource;
use App\Http\Resources\Api\Admins\Center\CenterResource;
use App\Http\Resources\Api\Admins\City\CitiesResource;
use App\Http\Resources\Api\Admins\Country\CountriesResource;
use App\Http\Resources\Api\Admins\Course\CoursesResource;
use App\Http\Resources\Api\Admins\DiveType\DiveTypesResource;
use App\Http\Resources\Api\Admins\Equipment\EquipmentsResource;
use App\Http\Resources\Api\Admins\School\SchoolsResource;
use App\Http\Resources\Api\Admins\Sites\SitesResource;
use App\Http\Resources\Api\Admins\Tank\TankResource;
use App\Http\Resources\Api\Diver\DashboardDiverResource;
use App\Http\Resources\Api\OrderCycle\Admin\TransactionsResource;
use App\Http\Resources\Api\OrderCycle\Admin\WalletLogResource;
use App\Http\Resources\Api\OrderCycle\DiverTicketCenterResource;
use App\Http\Resources\Api\OrderCycle\TransactionsLogResource;
use App\Http\Resources\Api\OrderCycle\WalletsResource;
use App\Interfaces\Admins\Statistics\AdminStatisticsRepositoryInterface;
use App\Models\Activity;
use App\Models\Amenty;
use App\Models\Article;
use App\Models\Center;
use App\Models\CenterReview;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\Diver;

use App\Models\DiveType;
use App\Models\Equipment;
use App\Models\School;
use App\Models\Site;
use App\Models\Tank;
use App\Models\TicketCenterRequest;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\Wallet;
use App\Models\WalletLog;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Laravel\Passport\Token;

class AdminStatisticsRepository
{




    private $dateTypes = [
        'weekly' => 'week',
        'monthly' => 'month',
        'daily' => 'day',
        'yearly' => 'year'

    ];

    private $months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];



    function search(Request $req, $model, $joinedTables = [], $selectedColumn = ['*'], $additionalFilters = [], $withResource = false, $groupBy = '')
    {
        $requestQuery = $this->getQuery($req);
        [$urlQuery, $date, $dateColeman, $dateType, $orderBy, $orderDirection, $page, $perPage] = $requestQuery;
        $q = $this->searchFilter($requestQuery, $model, $joinedTables, $selectedColumn, $additionalFilters, $withResource, $groupBy);

        if ($page)
            $q = $q->paginate($perPage)->withQueryString();
        else
            $q = $q->get();

        return $q;
    }

    function searchFilter($requestQuery,Model $model, $joinedTables = [], $selectedColumn = ['*'], $additionalFilters = [], $withResource = false, $groupBy = '')
    {



        //$table = $this->tables[$tableName];
        //$model = $table[0];
        [$filters, $date, $dateColeman, , $orderBy, $orderDirection, ,] = $requestQuery;
        $date = $date == Carbon::now()->year ? null : $date;

        $orderBy = explode('-', $orderBy);
        $dateColeman = explode('-', $dateColeman);
        $orderBy = count($orderBy) > 1 ? $orderBy[0] . '.' . $orderBy[1] : $model->getTable() . '.' . $orderBy[0];
        $dateColeman = count($dateColeman) > 1 ? $dateColeman[0] . '.' . $dateColeman[1] : $model->getTable() . '.' . $dateColeman[0];


        $q = $model->query();

        $q->selectRaw(join(',', $selectedColumn));

        foreach ($joinedTables as $table) {
            [$tableName, $leftTableId, $rightTableId] = $table;
            $q->leftJoin($tableName, $leftTableId, $rightTableId);
        }


        $this->searchByDate($q, $date, $dateColeman);

        foreach ($additionalFilters as $columnName => $columnValue)
            $this->filter($q, $columnName, $columnValue);

        foreach ($filters as $columnName => $columnValue)
            $this->filter($q, $columnName, $columnValue);


        if ($groupBy != '') {
            $q->groupBy($groupBy);
        }
        $q->orderBy($orderBy, $orderDirection);

        return $q;

    }

    function filter($q, $columnName, $columnValue)
    {
        $columnName = str_replace('-', '.', $columnName);
        $not = false;
        if (strpos($columnName, '!') === 0) {
            $not = true;
            $columnName = substr($columnName, 1);
        }
        if (is_array($columnValue)) {
            if (count($columnValue) == 2)
                $q->where($columnName, $columnValue[0], $columnValue[1]);

        } else {
            $arr = explode(',', $columnValue);
            if (count($arr) > 1) {
                foreach ($arr as &$item) {
                    $item = "'" . $item . "'";
                }
                $columnValue = join(',', $arr);


                $q->whereRaw('lower(' . $columnName . ') ' . ($not ? 'NOT' : '') . ' IN (' . $columnValue . ')');
            } else {
                if (is_numeric($columnValue)) {
                    if ($not)
                        $q->where($columnName, '<>', $columnValue);
                    else
                        $q->where($columnName, $columnValue);
                } else {
                    $columnValue = '%' . $columnValue . '%';

                    $q->whereRaw('lower(' . $columnName . ') ' . ($not ? 'NOT' : '') . '  LIKE "' . $columnValue . '"');
                }
            }
        }

    }


    function searchByDate(&$query, $date, $dateColeman)
    {
        if (isset($date)) {


            $dates = explode(',', $date);

            if (count($dates) > 1) {

                $query->whereRaw('date(' . $dateColeman . ') between ? and ?', $dates);
            } else {
                $date = $dates[0];

                if ($date == 'thisWeek')
                    $query->whereRaw('date(' . $dateColeman . ') between ? and ?', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                //$query->whereBetween($dateColeman, [Carbon::now()->startOfWeek() , Carbon::now()->endOfWeek()]);
                elseif ($date == 'thisMonth') {

                    $query->whereMonth($dateColeman, Carbon::now()->month)->whereYear($dateColeman, Carbon::now()->year);
                } elseif ($date == 'thisYear')
                    $query->whereYear($dateColeman, Carbon::now()->year);
                elseif ($date == 'thisDay') {

                    $query->whereDate($dateColeman, Carbon::now());
                } else {

                    $query->whereDate($dateColeman, $date);
                }

            }
        }
    }


    private function getQuery(Request $req)
    {


        $urlQuery = $req->query->all();

        $date = isset($urlQuery['date']) ? $urlQuery['date'] : Carbon::now()->year;
        $dateColeman = isset($urlQuery['dateColumn']) ? $urlQuery['dateColumn'] : 'created_at';
        $dateType = isset($urlQuery['dateType']) ? $urlQuery['dateType'] : null;
        $orderBy = isset($urlQuery['sort']) ? $urlQuery['sort'] : 'created_at';
        $orderDirection = isset($urlQuery['sortDesc']) ? ($urlQuery['sortDesc'][0] == 'true' ? 'desc' : 'asc') : 'asc';
        $page = isset($urlQuery['page']) ? $urlQuery['page'] : null;
        $perPage = isset($urlQuery['itemsPerPage']) ? $urlQuery['itemsPerPage'] : null;

        unset($urlQuery['page']);
        unset($urlQuery['sort']);
        unset($urlQuery['sortDesc']);
        unset($urlQuery['itemsPerPage']);
        unset($urlQuery['date']);
        unset($urlQuery['dateType']);
        unset($urlQuery['multiSort']);
        unset($urlQuery['mustSort']);
        unset($urlQuery['dateColumn']);


        return [$urlQuery, $date, $dateColeman, $dateType, $orderBy, $orderDirection, $page, $perPage];
    }

    private function getData(Request $req, $tableName, $joinedTables = [], $selectedColumn = ['*'], $additionalFilters = [], $searchDate = null, $withResource = false, $groupBy = '')
    {

        $requestQuery = $this->getQuery($req);
        [$urlQuery, $date, $dateColeman, $dateType, $orderBy, $orderDirection, $page, $perPage] = $requestQuery;
        $date = isset($searchDate) ? $searchDate : $date;
        $orderBy = $tableName . '.' . $orderBy;
        $dateColeman = $tableName . '.' . $dateColeman;
        [$query] = $this->searchFilter($requestQuery, $tableName, $joinedTables, $selectedColumn, $additionalFilters, $withResource, $groupBy);
        $ids = $query->get();
        $query = $this->tables[$tableName][0];


        if (isset($date)) {

            $dateType = isset($dateType) ? $this->dateTypes[$dateType] : null;
            $dates = explode(',', $date);

            if (count($dates) > 1) {
                $dateType = !isset($dateType) ? 'month' : $dateType;
                if ($dateType == 'day') {

                    $res = $query->selectRaw('(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                        ->whereIn($tableName . '.id', $ids)
                        ->whereRaw('date(' . $dateColeman . ') between ? and ?', $dates)
                        ->groupBy($dateType)
                        ->orderBy($orderBy, $orderDirection);

                    //return $this->proccessData($res->get(), $dateColeman, $dateType, $dates);

                } else {
                    $dateType = $dateType == 'year' || !isset($dateType) ? 'month' : $dateType;


                    $res = $query->selectRaw($dateType . '(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                        ->whereIn($tableName . '.id', $ids)
                        ->whereRaw('date(' . $dateColeman . ') between ? and ?', $dates)
                        ->groupBy($dateType)
                        ->orderBy($orderBy, $orderDirection);


                }
            } else {
                $date = $dates[0];

                if ($date == 'thisWeek') {
                    $dateType = 'day';

                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    $dates = [$startDate, $endDate];

                    $res = $query->selectRaw('(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                        ->whereIn($tableName . '.id', $ids)
                        ->whereRaw('date(' . $dateColeman . ') between ? and ?', $dates)
                        ->groupBy($dateType)
                        ->orderBy($orderBy, $orderDirection);
                    //$query->whereBetween($dateColeman, [Carbon::now()->startOfWeek() , Carbon::now()->endOfWeek()]);
                } elseif ($date == 'thisMonth') {

                    $dateType = $dateType == 'month' || $dateType == 'year' || !isset($dateType) ? 'week' : $dateType;
                    $year = Carbon::now()->year;
                    $month = Carbon::now()->month;
                    $startDate = new \DateTime("$year-$month-01");
                    $endDate = clone $startDate;
                    $endDate->modify('last day of this month');

                    $startDate = $startDate->format('Y-m-d');
                    $endDate = $endDate->format('Y-m-d');
                    $dates = [$startDate, $endDate];

                    $res = $query->selectRaw($dateType . '(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count,' . $dateColeman)
                        ->whereIn($tableName . '.id', $ids)
                        ->whereYear($dateColeman, $year)
                        ->whereMonth($dateColeman, $month)
                        ->groupBy($dateType)
                        ->orderBy($orderBy, $orderDirection);
                } elseif ($date == 'thisDay') {
                    $dateType = 'day';
                    $res = $query->selectRaw('(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                        ->whereIn($tableName . '.id', $ids)
                        ->whereDate($dateColeman, Carbon::now())
                        ->groupBy($dateType)
                        ->orderBy($orderBy, $orderDirection);

                } elseif ($date == 'thisYear') {
                    $dateType = !isset($dateType) ? 'month' : $dateType;
                    $year = Carbon::now()->year;
                    $startDate = "$year-01-01";
                    $endDate = "$year-12-31";
                    $dates = [$startDate, $endDate];

                    if ($dateType == 'day') {

                        $res = $query->selectRaw('(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                            ->whereIn($tableName . '.id', $ids)
                            ->whereYear($dateColeman, Carbon::now()->year)
                            ->groupBy($dateType)
                            ->orderBy($orderBy, $orderDirection);
                    } else {
                        $dateType = $dateType == 'year' || !isset($dateType) ? 'month' : $dateType;
                        $res = $query->selectRaw($dateType . '(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count , ' . $dateColeman)
                            ->whereIn($tableName . '.id', $ids)
                            ->whereYear($dateColeman, Carbon::now()->year)
                            ->groupBy($dateType)
                            ->orderBy($orderBy, $orderDirection);
                    }

                } else {


                    $date = explode('-', $date);


                    $res = null;
                    if (count($date) == 2) {

                        $year = $date[0];
                        $month = $date[1];

                        $startDate = new \DateTime("$year-$month-01");

                        $endDate = clone $startDate;
                        $endDate->modify('last day of this month');

                        $startDate = $startDate->format('Y-m-d');
                        $endDate = $endDate->format('Y-m-d');

                        $dates = [$startDate, $endDate];


                        $dateType = $dateType == 'month' || $dateType == 'year' || !isset($dateType) ? 'week' : $dateType;
                        $res = $query->selectRaw($dateType . '(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count,' . $dateColeman)
                            ->whereIn($tableName . '.id', $ids)
                            ->whereYear($dateColeman, $date[0])
                            ->whereMonth($dateColeman, $date[1])
                            ->groupBy($dateType)
                            ->orderBy($orderBy, $orderDirection);

                        dd($res->get());

                    } elseif (count($date) == 1) {


                        $year = $date[0];
                        $startDate = "$year-01-01";
                        $endDate = "$year-12-31";
                        $dates = [$startDate, $endDate];


                        if ($dateType == 'day') {

                            $res = $query->selectRaw('(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                                ->whereIn($tableName . '.id', $ids)
                                ->whereYear($dateColeman, $year)
                                ->groupBy($dateType)
                                ->orderBy($orderBy, $orderDirection);


                        } else {
                            $dateType = $dateType == 'year' || !isset($dateType) ? 'month' : $dateType;


                            $res = $query->selectRaw($dateType . '(date(' . $tableName . '.created_at)) as `' . $dateType . '`, count(*) as count ,' . $dateColeman)
                                ->whereIn($tableName . '.id', $ids)
                                ->whereYear($dateColeman, $date[0])
                                ->groupBy($dateType)
                                ->orderBy($orderBy, $orderDirection);


                        }


                    } else {

                        $dateType = 'day';
                        $res = $query->selectRaw('date(' . $dateColeman . ') as day' . ' , count(*) as count,' . $dateColeman)
                            ->whereDate($dateColeman, join('-', $date))
                            ->groupBy('day')
                            ->orderBy($orderBy);

                    }
//                    if ($page) {
//
//                        $res = $res->paginate($perPage)->withQueryString();
//
//                    } else
//                        $res = $res->get();
//
//                    return $this->proccessData($res, $dateColeman, $dateType);

                }

            }
            if ($page) {

                $res = $res->paginate($perPage)->withQueryString();

            } else
                $res = $res->get();


            return $this->proccessData($res, $dateColeman, $dateType, count($dates) > 1 ? $dates : null);
        }

        if ($page) {

            return $query->paginate($perPage)->withQueryString();
        }

        return $query->get();

    }

    private function setMissingDays($data, $dateDateList, $startDate, $endDate , $placeholder=null)
    {


        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $returnedData = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $date = $currentDate->format('Y-m-d');
            $index = array_search($date, $dateDateList);


            if (is_int($index)) {
                $returnedData[] = $data[$index];
            } else {
                if(!isset($placeholder))
                    $returnedData[] = ['day' => $date, 'count' => 0, 'date' => Carbon::parse($date)->format('F j Y')];
                else
                    $returnedData[] = array_merge( $placeholder,['day' => $date,  'date' => Carbon::parse($date)->format('F j Y')]);

            }
            $currentDate->addDay();
        }

        return $returnedData;
    }

    private function setMissingWeeks($data, $startDate, $endDate, $placeholder = null)
    {

        $data = $data->toArray();


        $start_date = new \DateTime($startDate);

        $end_date = new \DateTime($endDate);

        $start_week = $start_date->format('W');

        $end_week = $end_date->format('W');


        if ($start_week == $end_week)
            if ($start_week == 52)
                $start_week = 1;
            else
                $start_week++;
        $result = [];
        for ($week = $start_week; $week <= $end_week; $week++) {


            $start_date = new \DateTime($start_date->format('Y') . '-W' . ($week >= 1 && $week <= 9 ? '0' . $week : $week) . '-1');

            $end_date = new \DateTime($end_date->format('Y') . '-W' . ($week >= 1 && $week <= 9 ? '0' . $week : $week) . '-7');

            $date_range = $start_date->format('M d') . ',' . $end_date->format('M d');

            if (!in_array($week, array_column($data, 'week'))) {
                if(!isset($placeholder))
                    $result[] = ['week' => $week, 'count' => 0, 'date' => $date_range];
                else
                    $result[] = array_merge($placeholder,['week' => $week,  'date' => $date_range]);
            }
        }

        $result = array_merge($data, $result);

        usort($result, function ($a, $b) {
            return $a['week'] <=> $b['week'];
        });
        return $result;
    }


    private function setMissingMonths($data, $startDate, $endDate, $placeholder = null)
    {
        $data = $data->toArray();


        $start_date = new \DateTime($startDate);
        $end_date = new \DateTime($endDate);


        $start_month = $start_date->format('m');
        $end_month = $end_date->format('m');


        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            if (!in_array($month, array_column($data, 'month'))) {
                if(!isset($placeholder))
                    $result[] = ['month' => $month, 'count' => 0, 'date' => $this->months[$month - 1]];
                else
                    $result[] =  array_merge( $placeholder,['month' => $month, 'date' => $this->months[$month - 1]] );

            }
        }
        $result = array_merge($data, $result);


        usort($result, function ($a, $b) {
            return $a['month'] <=> $b['month'];
        });

        return $result;
    }

    private function proccessData($data, $dateColeman, $dateType, $dateRange = null)
    {

        $paginationData = null;
        $totalCount = 0;
        $dateList = [];

        if ($data instanceof LengthAwarePaginator) {
            $paginationData = [

                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'links' => $data->links(),
            ];
        }


        $data = $data->map(function ($row) use ($dateColeman, &$totalCount, $dateType, &$dateList) {


            $totalCount += $row->count;
            $dateColeman = explode('.', $dateColeman);
            $dateColeman = count($dateColeman) > 1 ? $dateColeman[1] : $dateColeman[0];

            $date = Carbon::parse($row[$dateColeman])->format('F j Y');

            $data = [];
            if ($dateType == 'month') {

                //$row->date = explode(" ", $date)[0];
                $data['month'] = $row->month;
                $data['count'] = $row->count;
                $data['date'] = explode(' ', $date)[0];
            } elseif ($dateType == 'week') {

                $startWeek = Carbon::parse(Carbon::parse($row[$dateColeman])->startOfWeek())->format('F j');

                $endWeek = Carbon::parse(Carbon::parse($row[$dateColeman])->endOfWeek())->format('F j');;

                //$row->date = $startWeek . "," . $endWeek;
                $data['week'] = $row->week;
                $data['count'] = $row->count;
                $data['date'] = $startWeek . ',' . $endWeek;
            } else {

                array_push($dateList, $row[$dateColeman]->toDateString());
                $row->day = $row[$dateColeman]->toDateString();
                $row->date = $date;
                $data['day'] = $row[$dateColeman]->toDateString();
                $data['count'] = $row->count;
                $data['date'] = $date;
            }


            //unset($row[$dateColeman]);
            return $data;
        });


        if (isset($dateRange)) {
            switch ($dateType) {
                case 'day':
                    $formattedData = $this->setMissingDays($data, $dateList, $dateRange[0], $dateRange[1]);
                    break;
                case 'week':

                    $formattedData = $this->setMissingWeeks($data, $dateRange[0], $dateRange[1]);
                    break;
                case 'month':

                    $formattedData = $this->setMissingMonths($data, $dateRange[0], $dateRange[1]);
                    break;
            }
        }


        if (isset($paginationData))
            return ['count' => $data->count(), 'total' => $totalCount, 'data' => $formattedData ?? $data, 'pagination' => $paginationData];
        else
            return ['count' => $data->count(), 'total' => $totalCount, 'data' => $formattedData ?? $data];

    }


    public function getTotalCenters(Request $req)
    {
        return ['data' => $this->getData($req, 'centers', [['center_users', 'centers.id', 'center_users.center_id'], ['center_cities', 'centers.id', 'center_cities.center_id'], ['cities', 'cities.id', 'center_cities.city_id'], ['countries', 'cities.country_id', 'countries.id']], ['DISTINCT centers.id'])];
    }

    public function getTotalDivers(Request $req)
    {
        return ['data' => $this->getData($req, 'divers', [['countries', 'divers.country_id', 'countries.id']], ['DISTINCT divers.id'])];
    }

    public function getCompoundDiversStats(Request $req)
    {
        return ['data' => $this->getCompoundStats($this->getData($req, 'divers', [['countries', 'divers.country_id', 'countries.id']], ['DISTINCT divers.id']))];
    }

    public function getCompoundCentersStats(Request $req)
    {
        return ['data' => $this->getCompoundStats($this->getData($req, 'centers', [['center_users', 'centers.id', 'center_users.center_id'], ['center_cities', 'centers.id', 'center_cities.center_id'], ['cities', 'cities.id', 'center_cities.city_id'], ['countries', 'cities.country_id', 'countries.id']], ['DISTINCT centers.id']))];
    }

    public function getCompoundOrdersStats(Request $req)
    {
        $joins = [
            ['centers', 'centers.id', 'divers_ticket_center_request.center_id'],
            ['center_cities', 'centers.id', 'center_cities.center_id'],
            ['cities', 'cities.id', 'center_cities.city_id'],
            ['countries', 'cities.country_id', 'countries.id'],
            ['center_users', 'center_users.center_id', 'centers.id']
        ];

        if ($req->query->has("status")) {
            if ($req->query->get("status") == "active") {
                $req->query->set("status", "accepted");
            } else if ($req->query->get("status") == "accepted") {
                $req->query->remove("status");
                $data = [
                    'paid_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'paid']),
                    'canceled_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'canceled']),
                    'active_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'accepted']),
                ];
                return ['data' => $this->getCompoundStats($this->getAcceptedOrders($data))];
            }
        }
        return ['data' => $this->getCompoundStats($this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id']))];
    }

    private function getCompoundStats($data)
    {
        $dataArr = &$data["data"];
        try {

            for ($i = 1; $i < count($dataArr); $i++) {
                $dataArr[$i]["count"] = $dataArr[$i]["count"] + $dataArr[$i - 1]["count"];
            }
            return $data;
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getWalletLogStatistics(Request $req, $filters = [])
    {
        return ['data' => $this->getData($req, 'wallets_logs',
            [['centers', 'centers.id', 'wallets_logs.center_id'], ['center_cities', 'centers.id', 'center_cities.center_id'],
                ['cities', 'cities.id', 'center_cities.city_id'],
                ['countries', 'cities.country_id', 'countries.id'],
                ['center_users', 'center_users.center_id', 'centers.id']
            ], ['DISTINCT wallets_logs.id'], $filters)];
        //return ["data" => $this->getData($this->walletLogs, $date, $dateColeman, $dateType, $orderBy, $orderDirection, $urlQuery + $filters, $page, $perPage)];
    }


    public function getWalletStatistics(Request $req)
    {
        return ['data' => $this->getData($req, 'wallets',
            [['centers', 'centers.id', 'wallets.center_id'], ['center_cities', 'centers.id', 'center_cities.center_id'],
                ['cities', 'cities.id', 'center_cities.city_id'],
                ['countries', 'cities.country_id', 'countries.id'],
                ['center_users', 'center_users.center_id', 'centers.id']
            ], ['DISTINCT wallets.id'])];
    }

    public function getTotalOrders(Request $req, $filters = [])
    {

        try {


            $joins = [
                ['centers', 'centers.id', 'divers_ticket_center_request.center_id'],
                ['center_cities', 'centers.id', 'center_cities.center_id'],
                ['cities', 'cities.id', 'center_cities.city_id'],
                ['countries', 'cities.country_id', 'countries.id'],
                ['center_users', 'center_users.center_id', 'centers.id']
            ];

            $data = [
                'created_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], $filters),
                'pending_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'pending'] + $filters),
                'paid_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'paid'] + $filters),
                'canceled_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'canceled'] + $filters),
                'active_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'accepted'] + $filters),
                'refused_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['!divers_ticket_center_request.created_at' => 'null', 'status' => 'refused'] + $filters)
            ];

            $data["accepted_orders"] = $this->getAcceptedOrders($data);

            return $data;
        } catch (Exception $e) {
            dd($e->getTrace());
        }
    }


    private function getAcceptedOrders($data)
    {
        $acceptedOrders = ["total" => 0, "data" => []];
        for ($i = 0; $i < count($data["paid_orders"]["data"]); $i++) {
            array_push($acceptedOrders["data"], $data["paid_orders"]["data"][$i]);
            $acceptedOrders["data"][$i]["count"] += $data["canceled_orders"]["data"][$i]["count"] + $data["active_orders"]["data"][$i]["count"];
            $acceptedOrders["total"] += $acceptedOrders["data"][$i]["count"];
        }
        return $acceptedOrders;
    }

    public function homePageStatistics(Request $req, $filters = [])
    {
        $joins = [
            ['centers', 'centers.id', 'divers_ticket_center_request.center_id'],
            ['center_cities', 'centers.id', 'center_cities.center_id'],
            ['cities', 'cities.id', 'center_cities.city_id'],
            ['countries', 'cities.country_id', 'countries.id'],
            ['center_users', 'center_users.center_id', 'centers.id']
        ];


        return [

            'today_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], $filters, 'thisDay'),
            'pending_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['status' => 'pending'] + $filters, 'thisYear'),
            'active_orders' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], ['status' => 'accepted'] + $filters, 'thisYear'),
            'orders_month' => $this->getData($req, 'divers_ticket_center_request', $joins, ['DISTINCT divers_ticket_center_request.id'], $filters, 'thisMonth'),

        ];
    }

    // Center Statistics


    public function getTotalActiveUsers()
    {
        return ['users_active' => [
            'total' => $this->token::query()->where('revoked', 0)->where('expires_at', '>', Carbon::now())->get()->count(),
            'adminsCount' => $this->getActiveUsers('admin'),
            'diversCount' => $this->getActiveUsers('diver'),
            'centersCount' => $this->getActiveUsers('center')
        ]];
    }


    public function getTransactionsStatistics(Request $req)
    {

        $dateType = $req->query->get("dateType");
        $date = $req->query->get("date");
        $req->query->remove("dateType");

        try {

            $transactionLog = $this->search($req, 'transactions_log', [
                ['transactions', 'transactions_log.transactions_id', 'transactions.id'],
                ["divers", "transactions_log.diver_id", "divers.id"],
                ["divers_ticket_center_request", "divers_ticket_center_request.id", "transactions.divers_ticket_center_request_id"],
                ["centers", "divers_ticket_center_request.center_id", "centers.id"],
                ["center_users", "center_users.center_id", "centers.id"],
                ["center_cities", "center_cities.center_id", "centers.id"]
            ],
                ['DISTINCT transactions_log.*', 'transactions.created_at as transactionsDate'])->groupBy('transactions_id');;


            $res = [];
            foreach ($transactionLog as $key => $value) {
                $totalInvoice = [];
                $totalInvoice['date'] = Carbon::parse($value[0]['transactionsDate'])->format('Y-m-d h:i:s');
                $totalInvoice['Amack'] = 0;
                $totalInvoice['Invoice'] = 0;
                foreach ($value as $log) {
                    $invoice = json_decode($log->invoice);

                    if (isset($invoice))
                        $totalInvoice['Invoice'] = +$invoice->pricing->siteTotalAfterAddedCostAndTanksCostAndGuidedDiveCostsAndSubEquipsSubDiscounting ?? 0;
                    $totalInvoice['Amack'] += $invoice->pricing->AllIncludingFeesAndVat ?? 0;
                }
                $totalInvoice['Center'] = $totalInvoice['Invoice'] - $totalInvoice['Amack'];
                $res[] = $totalInvoice;
            }

            if (isset($dateType) && count(explode("-", $date)) != 3) {
                return $this->getTransactionStatsRange($res, $dateType, $date);
            }

            return $res;

        } catch (Exception $e) {
            dd($e->getTrace());
        }
    }


    public function getTransactionsCompoundStats(Request $req){
        try {


            $data = $this->getTransactionsStatistics($req);


            for($i = 1; $i < count($data); $i++){
                if( isset($data[$i]["Amack"])) {
                    foreach (['Amack', 'Invoice', 'Center'] as $field) {
                        $data[$i][$field] += $data[$i - 1][$field];
                    }
                }
            }
            return $data;
        } catch (Exception $e) {
            dd($e->getTrace());
        }
    }


    private function getTransactionStatsRange($data, $dateType, $date)
    {

        $daysList = [];
        if($dateType == "daily")
            foreach ($data as $trans)
                $daysList[] = explode(" ", $trans["date"])[0];



        return $this->setMissingDates($this->calculateSumByPeriod($data, 'format', $dateType, $date), $dateType, $date , $daysList);
    }

    private function setMissingDates($data, $dateType, $date, $daysList = [])
    {
        $date = isset($date) ? $date : Carbon::now()->year;

        $date = explode(',', $date);
        if (count($date) == 1) {
            $date = $date[0];
            if ($date == 'thisWeek') {
                $dateType = 'daily';

                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $date = [$startDate, $endDate];
            } elseif ($date == 'thisMonth') {

                $dateType = $dateType == 'month' || $dateType == 'year' || !isset($dateType) ? 'week' : $dateType;
                $year = Carbon::now()->year;
                $month = Carbon::now()->month;
                $startDate = new \DateTime("$year-$month-01");
                $endDate = clone $startDate;
                $endDate->modify('last day of this month');

                $startDate = $startDate->format('Y-m-d');
                $endDate = $endDate->format('Y-m-d');
                $date = [$startDate, $endDate];
            } elseif ($date == 'thisYear') {
                $dateType = !isset($dateType) ? 'month' : $dateType;
                $year = Carbon::now()->year;
                $startDate = "$year-01-01";
                $endDate = "$year-12-31";
                $date = [$startDate, $endDate];
            } else {
                $date = explode('-', $date);
                if (count($date) == 2) {

                    $year = $date[0];
                    $month = $date[1];

                    $startDate = new \DateTime("$year-$month-01");

                    $endDate = clone $startDate;
                    $endDate->modify('last day of this month');

                    $startDate = $startDate->format('Y-m-d');
                    $endDate = $endDate->format('Y-m-d');
                    $date = [$startDate, $endDate];

                } elseif (count($date) == 1) {


                    $year = $date[0];
                    $startDate = "$year-01-01";
                    $endDate = "$year-12-31";
                    $date = [$startDate, $endDate];
                } else {
                    $dateType = "";
                }
            }
        }


        $data = is_array($data) ? collect($data) : $data;

        switch ($dateType) {
            case "monthly":

                return $this->setMissingMonths($data, $date[0], $date[1] , ["Amack" => 0,"Invoice" => 0, "Center" => 0]);

            case "weekly":

                return $this->setMissingWeeks($data, $date[0], $date[1] , ["Amack" => 0,"Invoice" => 0, "Center" => 0]);

            case "daily":

                return $this->setMissingDays($data,$daysList ,$date[0], $date[1] , ["Amack" => 0,"Invoice" => 0, "Center" => 0]);

        }
    }

    function calculateSumByPeriod($data, $period, $dateType, $dateFilter = null)
    {
        try {


            switch ($dateType) {
                case "monthly":
                    $format = 'Y-m';

                    break;
                case "weekly":
                    $format = 'Y-W';

                    break;
                case "daily":
                    $format = 'Y-m-d';

                    break;
            }

            $sums = [];

            foreach ($data as $entry) {
                $date = new \DateTime($entry['date']);
                $periodKey = $date->$period($format);

                if (!isset($sums[$periodKey])) {
                    $sums[$periodKey] = [
                        'Amack' => 0,
                        'Invoice' => 0,
                        'Center' => 0
                    ];
                    $this->setDateType($sums[$periodKey], $date, $dateType);
                }

                foreach (['Amack', 'Invoice', 'Center'] as $field) {
                    $sums[$periodKey][$field] += $entry[$field];
                }
            }


            return array_values($sums);
        } catch (Exception $e) {
            dd($e->getTrace());
        }
    }


    private function setDateType(&$data, $date, $type = "monthly")
    {

        if ($type == "monthly") {

            $data["month"] = $date->format('n');

            $data["date"] = $this->months[intval($data["month"]) - 1];

        } elseif ($type == "weekly") {

            $weekNumber = $date->format('W'); // Get the week number

            $weekStart = $date->format('M d'); // Get the start of the week
            $date->modify('+6 days'); // Move to the end of the week
            $weekEnd = $date->format('M d');
            $data["week"] = $weekNumber;

            $data["date"] = $weekStart . "," . $weekEnd;

        } else {

            $data["day"] = $date->format('Y-m-d');
            $data["date"] = $date->format('F j Y');
        }
    }


    private function getActiveUsers($authName)
    {
        return $this->search(new Request(), 'oauth_access_tokens', [], ['id'], ['expires_at' => ['>', Carbon::now()], 'name' => $authName, 'revoked' => 0])->count();
    }


}
