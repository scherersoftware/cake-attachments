<?php
namespace Attachments\Model\Table;

use Attachments\Model\Entity\Attachment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attachments Model
 */
class AttachmentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('attachments');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')
            ->requirePresence('filepath', 'create')
            ->notEmpty('filepath')
            ->requirePresence('filename', 'create')
            ->notEmpty('filename')
            ->requirePresence('filetype', 'create')
            ->notEmpty('filetype')
            ->add('filesize', 'valid', ['rule' => 'numeric'])
            ->requirePresence('filesize', 'create')
            ->notEmpty('filesize')
            ->requirePresence('model', 'create')
            ->notEmpty('model')
            ->add('foreign_key', 'valid', ['rule' => 'uuid'])
            ->requirePresence('foreign_key', 'create')
            ->notEmpty('foreign_key');

        return $validator;
    }
}
