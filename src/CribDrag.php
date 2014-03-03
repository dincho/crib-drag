<?php

class CribDrag
{
    protected $plaintexts;
    protected $ciphertexts;
    protected $xorPlaintexts;
    protected $crib;
    protected $candidates;

    public function __construct($ciphertext1, $ciphertext2)
    {
        $this->plaintexts = array();
        $this->setCiphertexts(array(
            (string) $ciphertext1,
            (string) $ciphertext2,
        ));
        $this->candidates = array();
    }

    public function setCiphertexts(array $ciphertexts)
    {
        $this->ciphertexts = $ciphertexts;
        $this->xorPlaintexts = self::strxor($ciphertexts[0], $ciphertexts[1]);
        $this->initPlaintexts(count($ciphertexts), strlen($this->xorPlaintexts));
    }

    public function setCrib($crib)
    {
        $this->crib = $crib;
    }

    public function generateCandidates()
    {
        $this->candidates = array();
        $len = strlen($this->xorPlaintexts) - strlen($this->crib);
        
        for ($i = 0; $i <= $len; $i++) {
            $chunk = substr($this->xorPlaintexts, $i);
            $this->candidates[] = self::strxor($chunk, $this->crib);
        }

        return $this->candidates;
    }

    public function getCandidates()
    {
        return $this->candidates;
    }

    public function applyCrib($idx, $pos)
    {
        if ($idx > count($this->ciphertexts) - 1) {
            throw new Exception("Invalid index.");
        }

        if ($pos > strlen($this->xorPlaintexts) - 1) {
            throw new Exception("Invalid position.");
        }

        $candidates = $this->getCandidates();
        $guess = $candidates[$pos];

        $text = $this->fillPlaintext($this->plaintexts[$idx], $pos, $this->crib);
        $this->plaintexts[$idx] = $text;

        for ($i = 0; $i < count($this->plaintexts); $i++) {
            if ($i == $idx) {
                continue; //don't overwrite the crib
            }

            $text = $this->fillPlaintext($this->plaintexts[$i], $pos, $guess);
            $this->plaintexts[$i] = $text;
        }
    }

    public function getPlaintexts()
    {
        return $this->plaintexts;
    }

    public function getKey()
    {
        return self::strxor($this->plaintexts[0], $this->ciphertexts[0]);
    }

    protected function fillPlaintext($text, $pos, $chunk)
    {
        $start = substr($text, 0, $pos);
        $end = substr($text, $pos + strlen($chunk));

        return $start . $chunk . $end;
    }

    protected function initPlaintexts($num, $len)
    {
        for ($i = 0; $i < $num; $i++) { 
            $this->plaintexts[$i] = str_repeat("-", $len);
        }
    }

    public static function strxor($a, $b)
    {
        $res = "";
        $n = min(strlen($a), strlen($b));

        for ($i = 0; $i < $n; $i++) { 
            $res .= chr(ord($a[$i]) ^ ord($b[$i]));
        }

        return $res;
    }
}
