<?php

abstract class PhabricatorInlineComment
  extends Phobject
  implements
    PhabricatorMarkupInterface {

  const MARKUP_FIELD_BODY = 'markup:body';

  const STATE_UNDONE = 'undone';
  const STATE_DRAFT = 'draft';
  const STATE_UNDRAFT = 'undraft';
  const STATE_DONE = 'done';

  private $storageObject;
  private $syntheticAuthor;
  private $isGhost;

  public function __clone() {
    $this->storageObject = clone $this->storageObject;
  }

  public function setSyntheticAuthor($synthetic_author) {
    $this->syntheticAuthor = $synthetic_author;
    return $this;
  }

  public function getSyntheticAuthor() {
    return $this->syntheticAuthor;
  }

  public function setStorageObject($storage_object) {
    $this->storageObject = $storage_object;
    return $this;
  }

  public function getStorageObject() {
    if (!$this->storageObject) {
      $this->storageObject = $this->newStorageObject();
    }

    return $this->storageObject;
  }

  abstract protected function newStorageObject();
  abstract public function getControllerURI();

  abstract public function setChangesetID($id);
  abstract public function getChangesetID();

  abstract public function supportsHiding();
  abstract public function isHidden();

  public function isDraft() {
    return !$this->getTransactionPHID();
  }

  public function getTransactionPHID() {
    return $this->getStorageObject()->getTransactionPHID();
  }

  public function isCompatible(PhabricatorInlineComment $comment) {
    return
      ($this->getAuthorPHID() === $comment->getAuthorPHID()) &&
      ($this->getSyntheticAuthor() === $comment->getSyntheticAuthor()) &&
      ($this->getContent() === $comment->getContent());
  }

  public function setIsGhost($is_ghost) {
    $this->isGhost = $is_ghost;
    return $this;
  }

  public function getIsGhost() {
    return $this->isGhost;
  }

  public function setContent($content) {
    $this->getStorageObject()->setContent($content);
    return $this;
  }

  public function getContent() {
    return $this->getStorageObject()->getContent();
  }

  public function getID() {
    return $this->getStorageObject()->getID();
  }

  public function getPHID() {
    return $this->getStorageObject()->getPHID();
  }

  public function setIsNewFile($is_new) {
    $this->getStorageObject()->setIsNewFile($is_new);
    return $this;
  }

  public function getIsNewFile() {
    return $this->getStorageObject()->getIsNewFile();
  }

  public function setFixedState($state) {
    $this->getStorageObject()->setFixedState($state);
    return $this;
  }

  public function setHasReplies($has_replies) {
    $this->getStorageObject()->setHasReplies($has_replies);
    return $this;
  }

  public function getHasReplies() {
    return $this->getStorageObject()->getHasReplies();
  }

  public function getFixedState() {
    return $this->getStorageObject()->getFixedState();
  }

  public function setLineNumber($number) {
    $this->getStorageObject()->setLineNumber($number);
    return $this;
  }

  public function getLineNumber() {
    return $this->getStorageObject()->getLineNumber();
  }

  public function setLineLength($length) {
    $this->getStorageObject()->setLineLength($length);
    return $this;
  }

  public function getLineLength() {
    return $this->getStorageObject()->getLineLength();
  }

  public function setAuthorPHID($phid) {
    $this->getStorageObject()->setAuthorPHID($phid);
    return $this;
  }

  public function getAuthorPHID() {
    return $this->getStorageObject()->getAuthorPHID();
  }

  public function setReplyToCommentPHID($phid) {
    $this->getStorageObject()->setReplyToCommentPHID($phid);
    return $this;
  }

  public function getReplyToCommentPHID() {
    return $this->getStorageObject()->getReplyToCommentPHID();
  }

  public function setIsDeleted($is_deleted) {
    $this->getStorageObject()->setIsDeleted($is_deleted);
    return $this;
  }

  public function getIsDeleted() {
    return $this->getStorageObject()->getIsDeleted();
  }

  public function setIsEditing($is_editing) {
    $this->getStorageObject()->setAttribute('editing', (bool)$is_editing);
    return $this;
  }

  public function getIsEditing() {
    return (bool)$this->getStorageObject()->getAttribute('editing', false);
  }

  public function getDateModified() {
    return $this->getStorageObject()->getDateModified();
  }

  public function getDateCreated() {
    return $this->getStorageObject()->getDateCreated();
  }

  public function openTransaction() {
    $this->getStorageObject()->openTransaction();
  }

  public function saveTransaction() {
    $this->getStorageObject()->saveTransaction();
  }

  public function save() {
    $this->getTransactionCommentForSave()->save();
    return $this;
  }

  public function delete() {
    $this->getStorageObject()->delete();
    return $this;
  }

  public function makeEphemeral() {
    $this->getStorageObject()->makeEphemeral();
    return $this;
  }


/* -(  PhabricatorMarkupInterface Implementation  )-------------------------- */


  public function getMarkupFieldKey($field) {
    $content = $this->getMarkupText($field);
    return PhabricatorMarkupEngine::digestRemarkupContent($this, $content);
  }

  public function newMarkupEngine($field) {
    return PhabricatorMarkupEngine::newDifferentialMarkupEngine();
  }

  public function getMarkupText($field) {
    return $this->getContent();
  }

  public function didMarkupText($field, $output, PhutilMarkupEngine $engine) {
    return $output;
  }

  public function shouldUseMarkupCache($field) {
    return !$this->isDraft();
  }

}