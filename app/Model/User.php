<?php

App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel
{
    var $name = 'User';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'A username is required'
            ),
            'checkExist' => array(
                'rule' => array('checkExistUsername'),
                'message' => 'This user already taken!',
            ),
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'A password is required'
            ),
        ),
        'confirmPassword' => array(
            'required' => array(
                'rule' => 'notBlank',
                'on' => 'create',
                'message' => 'Confirm password is required'
            ),
            'checkConfirmPassword' => array(
                'rule' => array('checkConfirmPassword'),
                'message' => 'Password mismatch',
            ),
        ),
    );

    public function checkExistUsername($check){
        $data = $this->find('first', array(
            'conditions' => array('username' => $check['username']),
        ));
        return !count($data);
    }

    public function checkConfirmPassword(){
        return $this->data['User']['confirmPassword'] == $this->data['User']['password'];
    }

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        $data[$this->alias]['date_created'] = time();
        $data[$this->alias]['date_modified'] = time();
        return true;
    }
}