<?php

namespace App\Controllers;

use App\Models\Student;
use CodeIgniter\RESTful\ResourceController;

class Students extends ResourceController
{
    private $student;

    public function __construct()
    {
        // membuat object student dari model
        $this->student = new Student();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $students = $this->student->findAll();
        return $this->respond($students);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $student = $this->student->find($id);
        if($student){
            return $this->respond($student);
        }
        return $this->failNotFound('Sorry! student not found');
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        // memvalidasi form
        $validation = $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[students.email]|min_length[6]',
        ]);

        if(!$validation){
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // ambil data dari form yang sudah tervalidasi
        $student = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email')
        ];

        // memasukkan data ke model(database)
        // me-return studentId
        $studentId = $this->student->insert($student);

        // menangkap studentId jika data berhasil di simpan di database
        // dan memasukkan studentId di data student
        // kemudian merespon dengan data student
        if($studentId){
            $student['id'] = $studentId;
            return $this->respondCreated($student);
        }

        // jika insert ke database error maka tampilkan pesan error
        return $this->fail('Sorry! no student created');

    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        // cek apakah student dengan id yg diberikan ada di database
        $student = $this->student->find($id);

        // jika ada student dengan id tersebut maka:
        if($student){
            // validasi form yang ada
            $validation = $this->validate([
                'name' => 'required',
                'email' => 'required|valid_email'
            ]);

            // cek apakah validasi berhasil, jika tidak tampilkan validation error
            if(!$validation){
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // tangkap data student yg sudah tervalidasi
            $student = [
                'id' => $id,
                'name' => $this->request->getVar('name'),
                'email' => $this->request->getVar('email')
            ];

            // save data student ke database
            $response = $this->student->save($student);

            // cek apakah data student berhasil disimpan di database
            if($response){
                //jika berhasil disimpan, return data student yang tersimpan
                return $this->respond($response);
            }

            //jika data tidak tersimpan tampilkan pesan gagal
            return $this->fail('Update failed!');

        }

        // jika tidak ada student dengan id tersebut, tampilkan error
        return $this->failNotFound('Sorry! no student found');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $student = $this->student->find($id);
        if($student){
            $response = $this->student->delete($id);
            if($response){
                // jika berhasil return respond $studemt
                return $this->respond($student);
            }
            return $this->fail('Delete failed');
        }

        return $this->failNotFound('Sorry! No student found');
    }
}
