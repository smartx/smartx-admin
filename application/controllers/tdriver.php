<?php
class TDriver extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('TDriverModel');
		$this->load->model('UserModel');
                $this->load->model('RideModel');
	}

	public function take_ride(){
		$driverId = $newLocations = $this->input->post('tdriver_id');
		$rideId = $newLocations = $this->input->post('ride_id');
		$actionLat = $newLocations = $this->input->post('lat');
		$actionLng = $newLocations = $this->input->post('lng');
		return $this->TDriverModel->takeRide($driverId, $rideId, $actionLat, $actionLng);
	}

	public function create_tdriver()
	{
		$newLocations = $this->input->post('data');
		
		$newLocationsArray = json_decode($newLocations);
		echo $this->TDriverModel->createNewTDriver($newLocationsArray);

	}

	public function update_location()
	{
		$tdriverId = $this->input->post('id');
                $lat = $this->input->post('lat');
                $lng = $this->input->post('lng');
                
                $this->TDriverModel->updateTDriverLocation($tdriverId, $lat, $lng);
                $waitingRides = $this->RideModel->getWaitingRidesForTDriver($tdriverId);
//                print_r($waitingRides);
                $waitingRidesWhitinTDriverRange=array();
                $returningWaitingRidesWhitinTDriverRange=array();
                for($i=0;$i<count($waitingRides);$i++){
//                    print_r();
                    if(RIDE_SEARCH_RADIUS >= $this->distance($waitingRides[$i]->pickup_lat, $waitingRides[$i]->pickup_lng, $lat, $lng)){
                        array_push($waitingRidesWhitinTDriverRange,
                            (object)array("ride_id"=>$waitingRides[$i]->ride_id,
                                "lat"=>$waitingRides[$i]->pickup_lat,
                                "lng"=>$waitingRides[$i]->pickup_lng,
                                "name"=>$waitingRides[$i]->first_name,
                                "phone"=>$waitingRides[$i]->phone,
                                "address"=>$waitingRides[$i]->address,
                                "ref"=>$waitingRides[$i]->reference));
                        array_push($returningWaitingRidesWhitinTDriverRange,$waitingRides[$i]->ride_id);
                    }
                }
//                print_r($waitingRidesWhitinTDriverRange);
                $this->RideModel->addTDriverToRidePoll($tdriverId, $returningWaitingRidesWhitinTDriverRange);
//		// print_r($locationToGo);
//		if($locationToGo!= null && $this->distance($locationToGo->lat, $locationToGo->lng, $lat, $lng)<=0.02){
//			$this->TDriverModel->startRiding($tdriverId, $locationToGo->ride_id, $lat, $lng);
			$this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode((object)array("data"=>$waitingRidesWhitinTDriverRange)));
//		}else{
//			$contents = $this->output->set_content_type('application/json')
//		                  ->set_output(json_encode($locationToGo));
//		}
		
	}
        
        public function bot_update_location1()
	{
		$tdriverId = $this->input->post('id');
                $lat = $this->input->post('lat');
                $lng = $this->input->post('lng');
                
                $this->TDriverModel->updateTDriverLocation($tdriverId, $lat, $lng);
                $waitingRides = $this->RideModel->getWaitingRidesForTDriver($tdriverId);
//                print_r($waitingRides);
                $waitingRidesWhitinTDriverRange=array();
                $returningWaitingRidesWhitinTDriverRange=array();
                for($i=0;$i<count($waitingRides);$i++){
//                    print_r();
                    if(RIDE_SEARCH_RADIUS >= $this->distance($waitingRides[$i]->pickup_lat, $waitingRides[$i]->pickup_lng, $lat, $lng)){
                        array_push($waitingRidesWhitinTDriverRange,
                            (object)array("ride_id"=>$waitingRides[$i]->ride_id,
                                "lat"=>$waitingRides[$i]->pickup_lat,
                                "lng"=>$waitingRides[$i]->pickup_lng,
                                "name"=>$waitingRides[$i]->first_name,
                                "phone"=>$waitingRides[$i]->phone,
                                "address"=>$waitingRides[$i]->address,
                                "ref"=>$waitingRides[$i]->reference));
                        array_push($returningWaitingRidesWhitinTDriverRange,$waitingRides[$i]->ride_id);
                    }
                }
//                print_r($waitingRidesWhitinTDriverRange);
                
                $this->RideModel->addTDriverToRidePoll($tdriverId, $returningWaitingRidesWhitinTDriverRange);
//		// print_r($locationToGo);
                
                $locationToGo = $this->TDriverModel->getLocationToGo($tdriverId);
                $contents = $this->output->set_content_type('application/json')
		                  ->set_output(json_encode($locationToGo));
                
//		if($locationToGo!= null && $this->distance($locationToGo->lat, $locationToGo->lng, $lat, $lng)<=0.02){
//			$this->TDriverModel->startRiding($tdriverId, $locationToGo->ride_id, $lat, $lng);
//			$this->output
//		                  ->set_content_type('application/json')
//		                  ->set_output(json_encode((object)array("data"=>$waitingRidesWhitinTDriverRange)));
//		}else{
//			
//		}
//		
	}
        
        public function bot_update_location()
	{
		$tdriverId = $this->input->post('id');
                $lat = $this->input->post('lat');
                $lng = $this->input->post('lng');

		// $newLocationsArray = json_decode($newLocations);
		$locationToGo = $this->TDriverModel->updateTDriverLocation($tdriverId, $lat, $lng);
		// print_r($locationToGo);
		if($locationToGo!= null && $this->distance($locationToGo->lat, $locationToGo->lng, $lat, $lng)<=0.02){
			$this->TDriverModel->startRiding($tdriverId, $locationToGo->ride_id, $lat, $lng);
			$contents = $this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode(array()));
		}else{
			$contents = $this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode($locationToGo));
		}
		
	}

	public function get_closest()
	{
		$selectedPoint = $this->input->post('selectedPoint');
		$selectedPointArray = json_decode($selectedPoint);
		$selectedPointId=$selectedPointArray->selected_id;
		$selectedPointLat=$selectedPointArray->lat;
		$selectedPointLng=$selectedPointArray->lng;
		// print_r($selectedPoint);
		$driverLocations = $this->TDriverModel->getTDriverLocations();
		$N = count($driverLocations);
		$arr = array();
		// print_r($driverLocations);
		for ($i=0; $i < $N; $i++) {
			$location = $driverLocations[$i];
			if($location->id==$selectedPointId)
				continue;
			array_push($arr,array(
					'from'=>$selectedPointId,
					'to'=>$location->id,
					'distance'=>$this->distance($selectedPointLat, $selectedPointLng, $location->lat, $location->lng)
				)
			);
		}
		$contents = $this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode($arr));
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



	public function index()
	{
		echo "ola k ase";
	}
}
?>