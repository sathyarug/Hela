<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Org\Color::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'color_name' => $faker->colorName,
        'color_code' => $faker->realText(rand(10,15)),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Customer::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'customer_code' => $faker->numberBetween($min = 1000, $max = 9000),
        'customer_name' => $faker->company,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'customer_short_name'=> $faker->word,
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'type_of_service'=>$faker->numberBetween($min = 0, $max = 8),
        'business_reg_no'=>$faker->swiftBicNumber,
        'business_reg_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'customer_address1'=>$faker->address,
        'customer_address2'=>$faker->address,
        'customer_city'=>$faker->city,
        'customer_postal_code'=>$faker->postcode,
        'customer_state'=>$faker->state,
        'customer_country'=>$faker->country,
        'customer_contact1'=>$faker->phoneNumber,
        'customer_contact2'=>$faker->phoneNumber,
        'customer_contact3'=>$faker->phoneNumber,
        'customer_email'=>$faker->email,
        'customer_map_location'=>$faker->timezone,
        'customer_website'=>$faker->domainName,
        'company_code'=>$faker->userName,
        'operation_start_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'order_destination'=>$faker->country,
        'currency'=>$faker->numberBetween($min = 0, $max = 5),
        'currency'=>$faker->numberBetween($min = 0, $max = 5),
        'boi_reg_no'=>$faker->swiftBicNumber,
        'boi_reg_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'vat_reg_no'=>$faker->swiftBicNumber,
        'svat_no'=>$faker->swiftBicNumber,
        'managing_director_name'=>$faker->name,
        'managing_director_email'=>$faker->email,
        'finance_director_name'=>$faker->name,
        'finance_director_email'=>$faker->email,
        'finance_director_contact'=>$faker->phoneNumber,
        'additional_comments'=>$faker->realText($maxNbChars = 200, $indexSize = 2),
        'ship_terms_agreed'=>$faker->numberBetween($min = 0, $max = 20),
        'payemnt_terms'=>$faker->numberBetween($min = 0, $max = 20),
        'payment_mode'=>$faker->numberBetween($min = 0, $max = 20),
        'bank_acc_no'=>$faker->creditCardNumber,
        'bank_name'=>$faker->domainWord,
        'bank_branch'=>$faker->city,
        'bank_code'=>$faker->citySuffix,
        'bank_swift'=>$faker->swiftBicNumber,
        'bank_iban'=>$faker->swiftBicNumber,
        'bank_contact'=>$faker->phoneNumber,
        'intermediary_bank_name'=>$faker->domainWord,
        'intermediary_bank_address'=>$faker->address,
        'intermediary_bank_contact'=>$faker->phoneNumber,
        'buyer_posting_group'=>$faker->citySuffix,
        'business_posting_group'=>$faker->citySuffix,
        'approved_by'=>$faker->firstNameMale,
        'system_updated_by'=>$faker->firstNameMale ,
        'customer_creation_form'=>$faker->firstNameMale ,
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Company::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'company_code' => $faker->word,
        'group_id' => $faker->numberBetween($min = 0, $max = 20),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'company_name'=>$faker->company,
        'company_name'=>$faker->company,
        'company_address_1'=>$faker->address,
        'company_address_2'=>$faker->address,
        'city'=>$faker->city,
        'country_code'=>$faker->word,
        'company_fax'=>$faker->phoneNumber,
        'company_contact_1'=>$faker->phoneNumber,
        'company_contact_2'=>$faker->phoneNumber,
        'company_logo'=>$faker->word,
        'company_email'=>$faker->email,
        'company_web'=>$faker->domainName,
        'default_currency'=>$faker->word,
        'default_currency'=>$faker->word,
        'finance_month'=>$faker->monthName($max = 'now'),
        'default_currency'=>$faker->word,
        'company_remarks'=>$faker->word,
        'vat_reg_no'=>$faker->word,
        'tax_code'=>$faker->word,
        'company_reg_no'=>$faker->swiftBicNumber,
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Org\Location\Cluster::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'group_code' => $faker->word,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'source_id'=>$faker->numberBetween($min = 0, $max = 20),
        'group_name'=>$faker->word,
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Location::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'loc_code' => $faker->word,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'company_id'=>$faker->numberBetween($min = 0, $max = 20),
        'loc_name'=>$faker->word,
        'loc_type'=>$faker->word,
        'loc_address_1'=>$faker->address,
        'loc_address_2'=>$faker->address,
        'city'=>$faker->city,
        'postal_code'=>$faker->postcode,
        'loc_phone'=>$faker->phoneNumber,
        'loc_fax'=>$faker->phoneNumber,
        'loc_email'=>$faker->email,
        'loc_web'=>$faker->domainName,
        'time_zone'=>$faker->timezone,
        'currency_code'=>$faker->word,
        'loc_google'=>$faker->latitude($min = -90, $max = 90),
        'currency_code'=>$faker->word,
        'state_Territory'=>$faker->state,
        'type_of_loc'=>$faker->numberBetween($min = 0, $max = 20),
        'country_code'=>$faker->word,
        'land_acres'=>$faker->word,
        'type_property'=>$faker->numberBetween($min = 0, $max = 20),
        'type_property'=>$faker->numberBetween($min = 0, $max = 20),
        'fix_asset'=>$faker->numberBetween($min = 0, $max = 20),
        'opr_start_date'=>$faker->dateTime($max = 'now', $timezone = null),
        'latitude'=>$faker->latitude($min = -90, $max = 90),
        'longitude'=>$faker->longitude($min = -180, $max = 180) ,
        'created_by'=>rand(0,10),

        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Source::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'source_code' => $faker->word,
        'source_name' => $faker->realText(rand(10,15)),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Department::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'dep_code' => $faker->numerify('Dep###'),
        'dep_name' => $faker->lexify('Department ???'),

        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Section::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'section_code' => $faker->numerify('Sec###'),
        'section_name' => $faker->lexify('Section ???'),

        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\ProductSpecification::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'prod_cat_description' => $faker->lexify('Description ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Division::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'division_code' => $faker->numerify('Div###'),
        'division_description' => $faker->lexify('Division discription ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Size::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'division_id' => $faker->numberBetween($min = 0, $max = 20),
        'size_name' => $faker->lexify('size ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Merchandising\ColorOption::class, function (Faker $faker) {
    return [

        'color_option' => $faker->lexify('color option ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\UOM::class, function (Faker $faker) {
    return [

        'uom_code' => $faker->numerify('UOM###'),
        'uom_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'uom_factor'=> $faker->lexify('test factor ???'),
        'uom_base_unit'=>$faker->lexify('test base unit ???'),
        'unit_type'=> $faker->lexify('test unit type ???'),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Cancellation\CancellationCategory::class, function (Faker $faker) {
    return [

        'category_code' => $faker->numerify('category_code###'),
        'category_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Cancellation\CancellationReason::class, function (Faker $faker) {
    return [

        'reason_code' => $faker->numerify('category_code###'),
        'reason_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'reason_category'=> $faker->numberBetween($min = 0, $max = 20),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});
