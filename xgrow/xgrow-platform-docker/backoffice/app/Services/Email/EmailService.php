<?php

namespace App\Services\Email;

use App\Repositories\EmailRepository;
use App\Services\Objects\EmailFilter;
use Illuminate\Database\Eloquent\Model;

class EmailService
{

    protected EmailRepository $email;

    public function __construct(
        EmailRepository $email
    )
    {
        $this->email = $email;
    }

    /**
     * @param $inputs
     * @return mixed
     * @throws Exception
     */
    public function getEmails($inputs)
    {
        $search = $inputs['search'] ?? null;

         $filter = (new EmailFilter())
             ->setSearch($search);

        return $this->email->listAll($filter)->get();
    }

    /**
     * Get email data
     * @param int $id
     * @param array|null $columns
     * @return Model
     */
    public function getEmail(int $id, ?array $columns = null): Model
    {
        $columns = $columns ?? ['*'];
        return $this->email->findById($id, $columns);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function store(array $data): Model
    {
        $email = $this->email->create($data);
        return $this->email->findById($email->id);
    }
    
    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $this->email->update($id, $data);
        return $this->email->findById($id);
    }
    
    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->email->delete($id);
    }


}
