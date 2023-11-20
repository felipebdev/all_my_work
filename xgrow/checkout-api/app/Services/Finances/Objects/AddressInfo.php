<?php


namespace App\Services\Finances\Objects;


use App\Services\Objects\FillableObject;

class AddressInfo extends FillableObject
{
    public static function fromRequestData(array $data): self
    {
        return (new self())
            ->withZipcode($data['address_zipcode'] ?? '')
            ->withCountry($data['country'] ?? '')
            ->withCity($data['address_city'] ?? '')
            ->withState($data['address_state'] ?? '')
            ->withNumber($data['address_number'] ?? '')
            ->withStreet($data['address_street'] ?? '')
            ->withDistrict($data['address_district'] ?? '')
            ->withComp($data['address_comp'] ?? '');
    }

    protected string $country;
    protected string $city;
    protected string $state;
    protected string $zipcode;
    protected string $number;
    protected string $street;
    protected string $district;
    protected string $comp;

    /**
     * This class must not be instantiated directly, use static factory instead.
     */
    protected function __construct(?array $data = [])
    {
        parent::__construct($data);
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    private function withCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    private function withCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    private function withState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getZipcode(): string
    {
        return $this->zipcode ?? '';
    }

    private function withZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number ?? '0';
    }

    private function withNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getStreet(): string
    {
        return $this->street ?? 'Rua não informada';
    }

    private function withStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getDistrict(): string
    {
        return $this->district ?? 'Bairro não informado';
    }

    private function withDistrict(string $district): self
    {
        $this->district = $district;
        return $this;
    }

    public function getComp(): string
    {
        return $this->comp;
    }

    private function withComp(string $comp): self
    {
        $this->comp = $comp;
        return $this;
    }

}
