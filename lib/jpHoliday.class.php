<?php

class jpHoliday
{
  protected static $holidays = array();
  
  public static function getHolidaysForYear($year = null)
  {
    if(!isset(self::$holidays[$year]))
    {
      self::loadHolidaysForYear($year);
    }
    
    return self::$holidays[$year];
  }
  
  public static function isHoliday($date)
  {
    $ts = strtotime($date);
    if($ts)
    {
      $year = date('Y', $ts);
      if(!isset(self::$holidays[$year]))
      {
        self::loadHolidaysForYear($year);
      }
    }
    return isset(self::$holidays[$year][$date]);
  }
  
  public static function getHolidayName($date)
  {
    if(self::isHoliday($date))
    {
      $ts = strtotime($date);
      if($ts)
      {
        $year = date('Y', $ts);
        return self::$holidays[$year][$date];
      }
    }
  }
  
  protected static function loadHolidaysForYear($year)
  {
    $settings = sfConfig::get('app_jp_holiday_plugin_national_holidays', array());
    $holidays = array();
    foreach($settings as $row)
    {
      switch($row['type'])
      {
        case 'happymonday':
          $ts = mktime(0, 0, 0, $row['month'], 1, $year);
          $firstMondayTs = strtotime('first monday', $ts);
          if($firstMondayTs)
          {
            $date = date('Y-m-d', $firstMondayTs + 60*60*24*7*($row['seq'] - 1));
            $holidays[$date] = $row['caption'];
          }
          
          break;
        case 'fixed':
          $date = date('Y-m-d', mktime(0, 0, 0, $row['month'], $row['date'], $year));
          $holidays[$date] = $row['caption'];
          break;
        default:
      }
    }
    self::$holidays[$year] = $holidays;
  }
}