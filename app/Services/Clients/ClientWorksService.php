<?php

namespace App\Services\Clients;

use App\Models\Auth\User;
use App\Models\Client\Client;
use App\Models\Client\ClientWork;
use App\Models\Finance\Client\FinClientWorkPrice;
use App\Services\Finances\Clients\FinClientWorksPaymentsService;
use App\Services\Finances\Clients\FinClientWorksPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientWorksService extends Service
{
    public static function index(Request $request) {
        $query = ClientWork::with('client')
            ->join('clients', 'clients.id', '=', 'client_works.client_id');
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::filtering($input, $query);
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $query->select('client_works.*');
        $users = User::select('id', 'name')->get();
        $works = $query->get()
            ->map(function ($work) use($users) {
                $str = '';
                if ($work->worker && $work->worker !== null) {
                    $ids = unserialize($work->worker);
                    if (is_array($ids) && count($ids) > 0) {
                        $usrs = $users->whereIn('id', $ids);
                        if (count($usrs) > 0) {
                            $index = 0;
                            foreach ($usrs as $usr) {
                                if ($index > 0) {
                                    $str .= ', ';
                                }
                                $str .= $usr->name;
                                $index++;
                            }
                        }
                    }
                }
                $work->workers_names = $str;
                return [
                    'id' => $work->id,
                    'visit_date' => $work->visit_date,
                    'visit_purpose' => $work->visit_purpose,
                    'where_knows' => $work->client->where_knows,
                    'full_name' => $work->client->full_name,
                    'phone' => $work->client->phone,
                    'result' => $work->result,
                    'workers_names' => $work->workers_names,
                    'fee' => $work->latestFinClientWorkPrice->price ?? null,
                    'fee_status' => $work->latestFinClientWorkPrice->status ?? null,
                    'client_id' => $work->client_id,
                ];
            });

        $count_query = ClientWork::with('client');
        if (count($input) > 0) {
            $count_query = self::filtering($input, $count_query);
        }
        $count = $count_query->get()->map(function ($work) {
                return [
                    'id' => $work->id,
                ];
            })->count();
        return self::sendResponse($works->toArray(), 'Client works returned successfully', $count);
    }
    public static function get_by_client(Request $request) {
        $input = $request->all();
        if (!isset($input['client_id']) || $input['client_id'] === '') {
            return self::sendError('No client_id sent');
        }
        $query = ClientWork::select('*')
            ->where('client_id', '=', $input['client_id']);
        if (count($input) > 0) {
            $query = self::filtering($input, $query);
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $users = User::select('id', 'name')->get();
        $works = $query->get()->map(function ($work) use ($users) {
            $str = '';
            if ($work->worker && $work->worker !== null) {
                $ids = unserialize($work->worker);
                if (is_array($ids) && count($ids) > 0) {
                    $usrs = $users->whereIn('id', $ids);
                    if (count($usrs) > 0) {
                        $index = 0;
                        foreach ($usrs as $usr) {
                            if ($index > 0) {
                                $str .= ', ';
                            }
                            $str .= $usr->name;
                            $index++;
                        }
                    }
                }
            }
            $work->workers_names = $str;
            return [
                'id' => $work->id,
                'visit_date' => $work->visit_date,
                'visit_date_end' => $work->visit_date_end,
                'visit_purpose' => $work->visit_purpose,
                'result' => $work->result,
                'worker' => $work->worker,
                'workers_names' => $work->workers_names,
                'fee' => $work->latestFinClientWorkPrice->price ?? null,
                'fee_status' => $work->latestFinClientWorkPrice->status ?? null,
                'client_id' => $work->client_id,
            ];
        });

        $count_query = ClientWork::where('client_id', '=', $input['client_id']);
        if (count($input) > 0) {
            $count_query = self::filtering($input, $count_query);
        }
        $count = $count_query->get()->count();
        return self::sendResponse($works->toArray(), 'Client works returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $work = ClientWork::find($id);

        $users = User::select('id', 'name')->get();
        $str = '';
        if ($work->worker && $work->worker !== null) {
            $ids = unserialize($work->worker);
            if (is_array($ids) && count($ids) > 0) {
                $usrs = $users->whereIn('id', $ids);
                if (count($usrs) > 0) {
                    $index = 0;
                    foreach ($usrs as $usr) {
                        if ($index > 0) {
                            $str .= ', ';
                        }
                        $str .= $usr->name;
                        $index++;
                    }
                }
            }
        }
        $work->workers_names = $str;

        $work->worker = unserialize($work->worker);
        $work->what_done = unserialize($work->what_done);
        $work->fee = $work->latestFinClientWorkPrice->price ?? null;
        $work->status = $work->latestFinClientWorkPrice->status ?? null;
        return self::sendResponse($work, 'Client work returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'visit_date' => 'required',
            'visit_purpose' => 'required',
            'client_id' => 'required'
        ]);
        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['worker']) && $input['worker'] !== '') {
            $worker = array();
            foreach ($input['worker'] as $wk) {
                $worker[] = $wk['id'];
            }
            $input['worker'] = serialize($worker);
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }

        $work = ClientWork::create($input);

        if (isset($input['fee'])) {
            $fee = ($input['fee'] !== '') ? $input['fee'] : 0;
            $for_save = array('date' => $input['visit_date'], 'price' => $fee, 'client_work_id' => $work->id);
            $price = FinClientWorksPricesService::save_new($for_save);

            if (isset($input['payment']) && $price !== false) {
                $fee = ($input['payment'] !== '') ? $input['payment'] : 0;
                $payment = FinClientWorksPaymentsService::save_new(array('date' => $input['visit_date'], 'amount' => $fee, 'fin_client_work_price_id' => $price->id));
            }
        }

        return self::sendResponse($work, 'Client work created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $work = ClientWork::find($id);

        $validator = Validator::make($input, [
            'visit_date' => 'required',
            'visit_purpose' => 'required',
            'client_id' => 'required'
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['worker']) && $input['worker'] !== '') {
            $worker = array();
            foreach ($input['worker'] as $wk) {
                $worker[] = $wk['id'];
            }
            $input['worker'] = serialize($worker);
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }


        $work->visit_date = $input['visit_date'];
        $work->visit_date_end = $input['visit_date_end'];
        $work->visit_purpose = $input['visit_purpose'];
        $work->what_done = $input['what_done'];
        $work->result = $input['result'];
        $work->worker = $input['worker'];
        $work->client_id = $input['client_id'];

        $work->save();

        return self::sendResponse($work, 'Client work updated successfully.');
    }

    public static function destroy($id) {
        $work = ClientWork::find($id);

        FinClientWorkPrice::where('client_work_id', $id)
            ->each(function ($price_item) {
                $price_item->payments()->delete();
                $price_item->delete();
            });
        if (!$work) {
            return self::sendError('Client work not found.');
        }
        $work->delete();

        return response()->json(['success' => true,'message' => 'Client work deleted successfully.'], 200);
    }
}
