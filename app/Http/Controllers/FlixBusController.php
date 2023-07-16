<?php

namespace App\Http\Controllers;

use App\Models\bus;
use App\Models\BusCity;
use App\Models\User;
use App\Services\Flixbus;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FlixBusController extends Controller
{
    private $flixbus;

    public function __construct(Flixbus $flixbus)
    {
        $this->flixbus = $flixbus;
    }
    //
    public function index()
    {
        $cities = BusCity::get();
        return view('front.flixbus.index', compact('cities'));
    }

    public function searchTrip(Request $request)
    {
        $search_by = 'cities';
        $from = $request->from;
        $to = $request->destination;
        $departure_date = Carbon::createFromFormat('d.m.Y', $request->departureDate)->format('d.m.Y');
        $adult = $request->adultCount;
        $children = $request->childCount;
        $bikes = 0;
        $response = $this->flixbus->searchTrips($search_by, $from, $to, $departure_date, $adult, $children, $bikes);
        return response()->json($response);
    }

    public function getCities()
    {
        $response = $this->flixbus->getCities();
        return response()->json($response);
    }

    public function updateReservationItems(Request $request)
    {
        $data = $request->validate([
            'trip_uid' => 'required|string',
            'adult' => 'required|integer',
            'currency' => 'required|string',

        ]);

        $result = $this->flixbus->updateReservationItems(
            $data['trip_uid'],
            $data['adult'],
            $data['children'] ?? 0,
            $data['currency'],
        );

        return response()->json($result);
    }
    public function initiatePayment(Request $request)
    {
        $validatedData = $request->validate([
            'reservation' => 'required',
            'reservation_token' => 'required',
            'email' => 'required|email',
            'payment.psp' => 'required',
            'payment.method' => 'required',
        ]);

        try {
            $response = $this->flixbus->startPayment(
                $validatedData['reservation'],
                $validatedData['reservation_token'],
                $validatedData['email'],
                $validatedData['payment']['psp'],
                $validatedData['payment']['method']
            );

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'There was an error while trying to start the payment',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function addPassanger(Request $request)
    {

        try {
            $response = $this->flixbus->addPassanger();

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'There was an error while trying to add passanger',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitTicket(Request $request)
    {

        $data = $request->validate([
            'trip_uid' => 'required|string',
            'adult' => 'required|integer',
            'children' => 'nullable',
            'departure_date' => 'required',
            'ticket_price' => 'required',

        ]);

        // $this->flixbus->updateReservationItems(
        //     $data['trip_uid'],
        //     $data['adult'],
        //     $data['children'] ?? 0,
        //     'EUR',
        //     $request->passengers,
        //     $request->email
        // );
        // $result = $this->flixbus->responseData;
        $result = array(
            'code' => 200,
            'message' => 'OK',
            'order_uid' => '9000000208-8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
            'order' => array(
                'id' => '9000000208',
                'download_hash' => '8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                'trips' => array(
                    0 => array(
                        'ride_uuid' => '69b65137-abd5-46ab-b704-c72010936f21',
                        'departure_station' => array(
                            'id' => 1,
                            'uuid' => 'dcbaaee6-9603-11e6-9066-549f350fcb0c',
                            'name' => 'Berlin central bus station',
                            'address' => 'Masurenallee 4-6',
                            'full_address' => 'Masurenallee 4-6, 14057 Berlin, Germany',
                            'coordinates' => array(
                                'latitude' => 52.507171,
                                'longitude' => 13.279399,
                            ),
                            'country' => array(
                                'name' => 'Germany',
                                'alpha2_code' => 'DE',
                            ),
                            'warnings' => 'Gate information available here: https://zob.berlin/en/guests-visitors#departures',
                        ),
                        'arrival_station' => array(
                            'id' => 10,
                            'uuid' => 'dcbabbfa-9603-11e6-9066-549f350fcb0c',
                            'name' => 'Munich central bus station',
                            'address' => 'Arnulfstraße 21',
                            'full_address' => 'Arnulfstraße 21, 80335 München, Germany',
                            'coordinates' => array(
                                'latitude' => 48.14248,
                                'longitude' => 11.55001,
                            ),
                            'country' => array(
                                'name' => 'Germany',
                                'alpha2_code' => 'DE',
                            ),
                            'warnings' => 'Gate information available here: https://www.muenchen-zob.de/en/connections',
                        ),
                        'departure' => array(
                            'timestamp' => 1689573600,
                            'tz' => 'GMT+02:00',
                        ),
                        'arrival' => array(
                            'timestamp' => 1689600600,
                            'tz' => 'GMT+02:00',
                        ),
                        'passengers' => array(
                            0 => array(
                                'type' => 'adult',
                                'uuid' => '8b91a891-2b5e-497e-8162-7193cd499134',
                                'firstname' => 'TES',
                                'lastname' => 'TEST2',
                                'phone' => '',
                            ),
                        ),
                        'transfers' => array(
                        ),
                        'line_direction' => 'Route 234 direction Zurich',
                        'line' => array(
                            'number' => '234',
                            'direction' => 'Zurich',
                            'brand' => array(
                                'id' => 'a18f138c-68fa-4b45-a42f-adb0378e10d3',
                                'name' => 'FlixBus',
                                'color' => '73d700',
                                'color_light' => 'e5f9c0',
                                'color_dark' => '187d00',
                                'type' => 'core',
                            ),
                        ),
                        'bike_slots_count' => 0,
                        'passbook_url' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/passbook/get/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v/9000000208-direct:201263293:1:10.pkpass',
                        'order_status' => 'paid',
                        'push_channel_uid' => '/passengers/4292949713',
                        'warnings' => array(
                        ),
                        'trip_uid' => 'direct:201263293:1:10',
                        'products_description' => 'TES TEST2 (Adult)',
                        'products_description_html' => '<b>TES TEST2</b> <i>(Adult)</i>',
                        'real_time_info_available' => true,
                        'seats_per_relation' => array(
                            0 => array(
                                'from' => 1,
                                'to' => 10,
                                'seats' => array(
                                    0 => array(
                                        'category' => 'free_seat',
                                        'is_auto_assigned' => false,
                                    ),
                                ),
                            ),
                        ),
                        'operated_by' => array(
                            0 => array(
                                'key' => '',
                                'label' => 'FlixBus DACH GmbH',
                                'url' => '',
                                'address' => 'Karl-Liebknecht-Straße 33',
                            ),
                        ),
                        'self_checkin_available' => true,
                        'transfer_type' => 'direct',
                    ),
                ),
                'invoices' => array(
                    0 => array(
                        'ticket_id' => 'mfb-9000000214',
                    ),
                ),
                'info_blocks' => array(
                    0 => array(
                        'name' => 'time',
                        'html' => 'Please be at the stop 15 minutes prior to departure.<br />Your ticket expires at departure time and will be available for purchase again.',
                        'title' => '',
                    ),
                    1 => array(
                        'name' => 'suitcase',
                        'html' => 'pdf-luggage-info',
                        'title' => '',
                    ),
                    2 => array(
                        'name' => 'rebooking',
                        'html' => 'To change your reservation, please visit <a href="https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking/mobile/auth?orderId=9000000208&downloadHash=8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v">https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking</a>',
                        'title' => '',
                    ),
                    3 => array(
                        'name' => 'help',
                        'html' => 'FAQ: <a href="https://help.flixbus.com">https://help.flixbus.com</a><br/><em>* (Please note, that fees for calls from landlines as well as from mobile phones depend on individual provider‘s rates.)</em>',
                        'title' => '',
                    ),
                    4 => array(
                        'name' => 'cabotage-note',
                        'html' => 'This ticket allows the passenger to travel between the start location and destination indicated on the ticket. Boarding later or exiting early is not permitted due to legal requirements.',
                        'title' => '',
                    ),
                    5 => array(
                        'name' => 'invoice',
                        'html' => 'To download your invoice, please visit <a href="https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking">%rebooking_text_url%</a>.',
                        'title' => '',
                    ),
                ),
                'order_uid' => '9000000208-8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                'reminder_link' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v/Ticket-Berlin-Muenchen-9000000208.pdf',
                'invoice_link' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v/Invoice-9000000208.pdf',
                'qr_data' => 'https://shop-de.stg1.staging.greensystems-dev.flixtech.io/pdfqr/9000000208/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                'qr_image' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/qrcode/en/9000000208/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                'attachments' => array(
                    0 => array(
                        'title' => 'Passenger rights',
                        'rel' => 'booking:passengers-rights',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/passenger-rights/en_fb_rights.pdf',
                        'type' => 'application/pdf',
                    ),
                    1 => array(
                        'title' => 'QR Code URL',
                        'rel' => 'booking:qr:image',
                        'href' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/qrcode/en/9000000208/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                        'type' => 'image/png',
                    ),
                    2 => array(
                        'title' => 'QR Code Data',
                        'rel' => 'booking:qr:data',
                        'href' => 'https://shop-de.stg1.staging.greensystems-dev.flixtech.io/pdfqr/9000000208/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                        'type' => 'text/plain',
                    ),
                    3 => array(
                        'title' => 'Invoice',
                        'rel' => 'booking:invoice',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v/Invoice-9000000208.pdf',
                        'type' => 'application/pdf',
                    ),
                    4 => array(
                        'title' => 'Booking Confirmation',
                        'rel' => 'booking:confirmation',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v/Ticket-Berlin-Muenchen-9000000208.pdf',
                        'type' => 'application/pdf',
                    ),
                    5 => array(
                        'title' => 'Rebooking',
                        'rel' => 'shop:rebooking',
                        'href' => 'https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking/mobile/auth?orderId=9000000208&downloadHash=8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                        'type' => 'text/plain',
                    ),
                ),
            ),
            '_links' => array(
                'self' => array(
                    'href' => 'http://staging-stg1-mfb-api-web-webc-green-dev.origin-api-gw.ew1d2.k8s-dev.flix.tech/mobile/v2/orders/9000000208/info?download_hash=8cr5hgq7roxd9dlgt2ih446z9a1g1g0t2jy3t4k8gos6ahlt2v',
                ),
                'cancel' => array(
                    'href' => 'http://staging-stg1-mfb-api-web-webc-green-dev.origin-api-gw.ew1d2.k8s-dev.flix.tech/public/v2/orders/9000000208/cancel',
                ),
            ),
        );
        $trip = bus::create([
            'reseller_id' => auth()->user()->id,
            'departure_date' => $data['departure_date'],
            'total_passenger' => $data['adult'] + $data['children'],
            'total_price' => $data['ticket_price'] * ($data['adult'] + $data['children']),
            'user_email' => $request->email,
            'meta_data' => json_encode($result), // Storing entire result data as JSON in 'meta_data' column
        ]);
        $user = User::find($trip->reseller_id); // Assuming you have the user object available

        $totalPrice = $trip->total_price;
        $busCreditProfit = $user->bus_credit_profit / 100; // Divide by 100 to get the decimal value

        $userDiscount = $totalPrice * 0.07 * $busCreditProfit;
        $busCredit = $user->bus_credit - $totalPrice + $userDiscount;

        $user->update([
            'bus_credit' => $busCredit,
        ]);
        return response()->json($result);
    }
    public function busTicketList()
    {
        $reseller_id = auth()->user()->id;
        $busTickets = Bus::where('reseller_id', $reseller_id)->get();

        $data = [];
        foreach ($busTickets as $busTicket) {
            $metaData = json_decode($busTicket->meta_data, true);
            $departureStationName = $metaData['order']['trips'][0]['departure_station']['name'];
            $arrivalStationName = $metaData['order']['trips'][0]['arrival_station']['name'];
            $totalPassengers = sizeof($metaData['order']['trips'][0]['passengers']);

            $data[] = [
                'user_email' => $busTicket->user_email,
                'departure_station_name' => $departureStationName,
                'arrival_station_name' => $arrivalStationName,
                'departure_date' => $busTicket->departure_date,
                'total_passengers' => $totalPassengers,
                'ticket_unit_price' => $busTicket->total_price / $totalPassengers,
                'ticket_total_price' => $busTicket->total_price,
                'ticket_purchase_date' => $busTicket->created_at,
                'status' => $busTicket->cancel_status,

                'passenger_rights' => $metaData['order']['attachments'][0]['href'],
                'qr_code_url' => $metaData['order']['attachments'][1]['href'],
                'qr_code_data' => $metaData['order']['attachments'][2]['href'],
                'invoice' => $metaData['order']['attachments'][3]['href'],
                'booking_confirmation' => $metaData['order']['attachments'][4]['href'],

            ];
        }

        return view('front.flixbus.bus-ticket-list', ['busTickets' => $data]);
    }

}
