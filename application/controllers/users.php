<?php
class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('TDriverModel');
                $this->load->model('RideModel');
	}

	public function update_location()
	{
            $userId = $this->input->post('id');
            $lat = $this->input->post('lat');
            $lng = $this->input->post('lng');


            $affectedRows = $this->UserModel->updateUserLocation($userId, $lat, $lng);
            // print_r($locations);
            $this->output
                              ->set_content_type('application/json')
                              ->set_output(json_encode($affectedRows));
             // echo json_encode($data['tdrivers_locations']);
            }
	

	public function locations()
	{
		$locations = $this->UserModel->getUserLocations();
		// print_r($locations);
		$this->output->set_content_type('application/json')
		                  ->set_output(json_encode($locations));
		 // echo json_encode($data['tdrivers_locations']);
	}
        
        public function fetch_ride_status(){
            $userId = $this->input->get('id');
//            print_r($newLocations);
//            $userId = $newLocations['id'];
            
            // get valid ride (status 1,2, or 7) 
            $ride = $this->UserModel->getUserRide($userId);
//            print_r($ride);
            $n = count($ride);
//            print_r($n);
            if($n==0){
                $data = (object)array("data"=>array("status"=>0));
                $this->output->set_content_type('application/json')
		                  ->set_output(json_encode($data));
            }else{
                if($ride->status==1){
                    $data = (object)array("data"=>
                    array(
                        "status"=>1,
                        "ride_id"=>$ride->ride_id,
                        "ex_time"=>2)
                    );
                    $this->output->set_content_type('application/json')
		                  ->set_output(json_encode($data));
                }else if($ride->status==2){
                    $driver = $this->UserModel->getRideDriver($ride->ride_id);
                    $data = (object)array("data"=>
                    array(
                        "status"=>2,
                        "ride_id"=>$ride->ride_id,
                        "tdriver_id"=>$driver->tdriver_id,
                        "tdriver_name"=>$driver->name,
                        "tdriver_ll"=>$driver->lat.",".$driver->lng,
                        "tdriver_pic"=>"http://images1.wikia.nocookie.net/__cb20130206142552/bourne/images/9/95/Bourne-Passport.jpg",
                        "tdriver_phone"=>$driver->phone,
                        "ex_time"=>2,
                        "ex_pickup_time"=>3)
                    );
                    $this->output->set_content_type('application/json')
		                  ->set_output(json_encode($data));
                }
            }
//            $ride
        }
        
	public function create_user()
	{
		$newLocations = $this->input->post('data');
		
		$newLocationsArray = json_decode($newLocations);
		// print_r($newLocationsArray);
		echo $this->UserModel->createNewUser($newLocationsArray);
	}

	public function start_ride(){
		$userId = $this->input->post('u');
		$userAddressId = $this->input->post('i');
		if($userAddressId!=null){
			$address = $this->input->post('a');

			if($address==null){
				//Use existing address
				// Do nothing
			}else{
				//Modify existing address
				$reference = $this->input->post('r');
				$lat = $this->input->post('la');
				$lng = $this->input->post('ln');
				$userAddressId = $this->UserModel->updateUserAddress($userAddressId, $address, $reference, $lat, $lng);
			}
		}else{
			//Brand new address
			$address = $this->input->post('a');
			$reference = $this->input->post('r');
			$lat = $this->input->post('la');
			$lng = $this->input->post('ln');
			$userAddressId = $this->UserModel->createUserAddress($address, $reference, $lat, $lng, $userId);
		}
		
		// print_r($newLocationsArray);
		$rideInfo = $this->UserModel->startRide($userId, $userAddressId);

		$locations = $this->TDriverModel->getTDriverLocations();
		$N = count($locations);
		$closestTDriverIds = array();
		// print_r($locations);
		for ($i=0; $i < $N; $i++) {
			$location = $locations[$i];
			$distance = $this->distance($rideInfo->lat, $rideInfo->lng, $location->lat, $location->lng);
			if($distance<3.0){ // in km
				array_push($closestTDriverIds, $location->id);
			}
		}
		$this->RideModel->createInitialRequestPoll($rideInfo->rideId,$closestTDriverIds);

		//...Send Push Notifications to drivers here

		$res= array("rideId"=>$rideInfo->rideId, "assigned_tdrivers"=>$closestTDriverIds, "pickup_location"=>array("lat"=>$rideInfo->lat, "lng"=>$rideInfo->lng));
		$contents = $this->output
	              ->set_content_type('application/json')
	              ->set_output(json_encode($res));
	}

	public function clear_rides_requests(){
		$ridesDeleted = $this->UserModel->clearRequestPolls();
		echo $ridesDeleted." rides deleted";
	}


	public function clear_locations()
	{
		echo $this->UserModel->clearUserLocations();
	}

	public function get_closest()
	{
		$selectedPoint = $this->input->post('selectedPoint');
		$selectedPointArray = json_decode($selectedPoint);
		$selectedPointId=$selectedPointArray->tdriver_id;
		$selectedPointLat=$selectedPointArray->lat;
		$selectedPointLng=$selectedPointArray->lng;
		// print_r($selectedPoint);
		$locations = $this->TDriverModel->getTDriverLocations();
		$N = count($locations);
		$arr = array();
		// print_r($locations);
		for ($i=0; $i < $N; $i++) {
			$location = $locations[$i];
			if($location->tdriver_id==$selectedPointId)
				continue;
			array_push($arr,array(
					'from'=>intval($selectedPointId),
					'to'=>$location->tdriver_id,
					'distance'=>$this->distance($selectedPointLat, $selectedPointLng, $location->lat, $location->lng)
				)
			);
		}
		$contents = $this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode($arr), JSON_NUMERIC_CHECK);
		// echo json_encode($arr);
	}

	private function distance($lat1, $lon1, $lat2, $lon2) {
		$R = 6371; // km
		$dLat = $this->toRad($lat2 - $lat1);
		$dLon = $this->toRad($lon2 - $lon1);
		$lat1 = $this->toRad($lat1);
		$lat2 = $this->toRad($lat2);

		$a = sin($dLat / 2.0) * sin($dLat / 2.0)
				+ sin($dLon / 2.0) * sin($dLon / 2.0) * cos($lat1)
				* cos($lat2);
		$c = 2 * atan2(sqrt($a), sqrt(1.0 - $a));
		$d = $R * $c;
		return $d;
	}

	private function toRad($val) {
		return $val *  M_PI/ 180;
	}



	public function fakelogin($username)
	{
		// $userName = $this->input->get('username');
		$userId = $this->UserModel->login($username);
		$this->output
                  ->set_content_type('application/json')
                  ->set_output(json_encode((object)($userId)));
	}

	public function update_user_location()
	{
		$userId = $this->input->post('userId');
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');
		// $newLocationsArray = json_decode($newLocations);
		$this->UserModel->updateUserLocation($userId, $lat, $lng);
		echo "updated succesfully";
	}
}
?>