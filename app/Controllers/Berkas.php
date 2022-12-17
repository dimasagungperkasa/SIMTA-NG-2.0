<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BerkasModel;

class Berkas extends BaseController
{
	public function index()
	{
		$berkas = new BerkasModel();
		$data['berkas'] = $berkas->findAll();
		return view('view_berkas', $data);
	}
    public function create()
	{
		return view('form_upload');
	}
    function download($id)
	{
		$berkas = new BerkasModel();
		$data = $berkas->find($id);
		return $this->response->download('uploads/berkas/' . $data->berkas, null);
	}
	public function save()
	{
		

		$berkas = new BerkasModel();
		$dataBerkas = $this->request->getFile('berkas');
		
        if (!$this->validate([
			'keterangan' => [
				'rules' => 'required',
				'errors' => [
					'required' => '{field} Tidak boleh kosong'
				]
			],
			'berkas' => [
				'rules' => 'uploaded[berkas]|mime_in[berkas,application/pdf]|max_size[berkas,2048]',
				'errors' => [
					'uploaded' => 'Harus Ada File yang diupload',
					'mime_in' => 'Format File Harus Berupa pdf',
					'max_size' => 'Ukuran File Maksimal 2 MB'
				]

			]
		])) 
		{
			session()->setFlashdata('error', $this->validator->listErrors());
			return redirect()->back()->withInput();
		}else {
			$fileName = $dataBerkas->getRandomName();
		$berkas->insert([
			'berkas' => $fileName,
			'keterangan' => $this->request->getPost('keterangan')
		]);
			$dataBerkas->move(WRITEPATH. 'uploads/berkas/', $fileName);
			session()->setFlashdata('success', 'Berkas Berhasil diupload');
			return redirect()->back()->withInput();
			
		}
		
	}

}