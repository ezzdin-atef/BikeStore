<?php

namespace MVCPHP\controllers;


use MVCPHP\libraries\Controller;

class Products extends Controller {

  public function __construct() {
    $this->userModel = $this->model('product');
    $this->shopModel = $this->model('shop');
    $this->itemModel = $this->model('product');
  }


  public function index() {
    $this->show();
  }

  public function add() {
    if (isLoggedIn()) {

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = ['username' => getUsername(), 'name' => $_POST['name'], 'feature' => $_POST['feature'], 'price' => $_POST['price'], 'quantity' => $_POST['quantity'], 'photo' => '', 'forRent' => $_POST['renting'], 'isBike' => $_POST['isBike'], 'isNew' => $_POST['new'], 'name_err' => '', 'feature_err' => '', 'price_err' => '', 'quantity_err' => '', 'photo_err' => '',];

        $photoName = $_FILES['photo']['name'];
        $photoSize = $_FILES['photo']['name'];
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoType = $_FILES['photo']['type'];

        $photoAllowedExtention = array('jpeg', 'jpg', 'png', 'gif');
        $photoExtention = explode('.', $photoName);
        $photoExtention = end($photoExtention);
        $photoExtention = strtolower($photoExtention);

        if (empty($data['name'])) {
          $data['name_err'] = 'Please fill the name field';
        }
        if (empty($data['feature'])) {
          $data['feature_err'] = 'Please fill the feature field';
        }
        if (empty($data['quantity'])) {
          $data['quantity_err'] = 'Please fill the quantity field';
        } elseif ($data['quantity'] <= 0) {
          $data['quantity_err'] = 'Please fill the quantity field with values more than 0';
        }
        if (empty($data['price']) || $data['price'] <= 0) {
          $data['price_err'] = 'Please fill the price field';
        } elseif ($data['price'] <= 0) {
          $data['price_err'] = 'Please fill the price field with values more than 0';
        }

        if (!in_array($photoExtention, $photoAllowedExtention) && !empty($photoName)) {
          $data['photo_err'] = 'Sorry, The Extention Not Allowed :(';
        } elseif (empty($photoName)) {
          $data['photo_err'] = 'Please add a photo for the product';
        }

        if (empty($data['name_err']) && empty($data['feature_err']) && empty($data['quantity_err']) && empty($data['photo_err'])) {
          $randomNum = rand(0, 100000);
          move_uploaded_file($photoTmp, 'img/uploads/' . $randomNum . '_' . $photoName);
          $data['photo'] = $randomNum . '_' . $photoName;
          if ($this->userModel->add($data)) {
            redirect('pages');
          } else {
            flash('error', 'something went wrong', 'alert alert-danger');
            redirect('pages/index');
          }

        } else {
          $this->view('products/add', $data);
        }
      } else {
        $data = ['username' => getUsername(), 'name' => '', 'feature' => '', 'quantity' => '', 'forRent' => 1, 'isBike' => 1, 'isNew' => 1, 'name_err' => '', 'feature_err' => '', 'quantity_err' => '',];
        $this->view('products/add', $data);
      }

    } else {
      flash('error', 'Sorry, You need to login first', 'alert alert-danger');
      redirect('pages/index');
    }
  }

  public function show($username) {
    $data = ['username' => $username, 'products' => $this->userModel->allProductsByUsername($username)];
    $this->view('products/show', $data);
  }

  public function edit($id = -1) {
    if ($id == -1) {
      redirect('pages');
    } else {
      if (isLoggedIn()) {
        $item = $this->userModel->getProductById($id);
        if ($item->username == getUsername()) {
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = ['username' => getUsername(), 'id' => $id, 'name' => $_POST['name'], 'features' => $_POST['feature'], 'price' => $_POST['price'], 'quantity' => $_POST['quantity'], 'photo' => '', 'rentStatus' => $_POST['renting'], 'isBike' => $_POST['isBike'], 'isNew' => $_POST['isNew'], 'name_err' => '', 'features_err' => '', 'price_err' => '', 'quantity_err' => '', 'photo_err' => '',];

            $photoName = $_FILES['photo']['name'];
            $photoSize = $_FILES['photo']['name'];
            $photoTmp = $_FILES['photo']['tmp_name'];
            $photoType = $_FILES['photo']['type'];

            $photoAllowedExtention = array('jpeg', 'jpg', 'png', 'gif');
            $photoExtention = explode('.', $photoName);
            $photoExtention = end($photoExtention);
            $photoExtention = strtolower($photoExtention);

            if (empty($data['name'])) {
              $data['name_err'] = 'Please fill the name field';
            }
            if (empty($data['features'])) {
              $data['features_err'] = 'Please fill the feature field';
            }
            if (empty($data['quantity'])) {
              $data['quantity_err'] = 'Please fill the quantity field';
            } elseif ($data['quantity'] <= 0) {
              $data['quantity_err'] = 'Please fill the quantity field with values more than 0';
            }
            if (empty($data['price']) || $data['price'] <= 0) {
              $data['price_err'] = 'Please fill the price field';
            } elseif ($data['price'] <= 0) {
              $data['price_err'] = 'Please fill the price field with values more than 0';
            }

            if (!in_array($photoExtention, $photoAllowedExtention) && !empty($photoName)) {
              $data['photo_err'] = 'Sorry, The Extention Not Allowed :(';
            }

            if (empty($data['name_err']) && empty($data['features_err']) && empty($data['quantity_err']) && empty($data['photo_err'])) {
              if (!empty($photoName)) {
                $randomNum = rand(0, 100000);
                move_uploaded_file($photoTmp, 'img/uploads/' . $randomNum . '_' . $photoName);
                $data['photo'] = $randomNum . '_' . $photoName;
              }
              if ($this->userModel->edit($data)) {
                redirect('pages');
              } else {
                flash('error', 'something went wrong', 'alert alert-danger');
                redirect('pages/index');
              }

            } else {
              $this->view('products/add', $data);
            }


          } else {
            $data = ['id' => $id, 'name' => $item->name, 'features' => $item->features, 'price' => $item->price, 'quantity' => $item->quantity, 'photo' => $item->photoName, 'rentStatus' => $item->rentStatus, 'isBike' => $item->isBike, 'isNew' => $item->isNew, 'name_err' => '', 'features_err' => '', 'price_err' => '', 'quantity_err' => '', 'photo_err' => '',];
            $this->view('products/edit', $data);
          }


        } else {
          flash('error', 'You are not allow to get here', 'alert alert-danger');
          redirect('pages/index');
        }
      } else {
        flash('error', 'Sorry, You need to login first', 'alert alert-danger');
        redirect('pages/index');
      }

    }

  }

  public function delete($id = -1) {
    if ($id == -1) {
      redirect('pages');
    } else {
      if (isLoggedIn()) {
        $item = $this->userModel->getProductById($id);
        if ($item->username == getUsername()) {
          if ($this->userModel->delete($id)) {
            redirect('pages');
          } else {
            die('something went wrong');
          }
        } else {
          die('You are not allow to get here');
        }
      } else {
        die('You are not allow to get here');
      }
    }

  }

  public function rent($id = -1) {
    if ($id == -1) {
      redirect('pages');
    } else {
      if (isLoggedIn()) {
        $item = $this->userModel->getProductById($id);
        if ($item->rentStatus == 1) {
          $data = ['username' => getUsername(), 'productId' => $id,];
          $this->userModel->renting($data);
          redirect('pages');
        } else {
          die('');
          flash('error', 'this product is not for renting', 'alert alert-danger');
          redirect('pages/index');
        }
      } else {
        flash('error', 'Sorry, You need to login first', 'alert alert-danger');
        redirect('pages/index');
      }
    }

  }


}