<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# <font style='color:red'>***advanced-multifunction-facade***</font>
You can control user access from outside the called method, and you can also add listener classes before and after the method is called.

### <font style='color:yellow'> How to install this facade?</font>
    composer require hosseinsheikh/advanced-multifunction-facade
### <font style='color:yellow'> How to use this facade?</font>
- Create simple Facade:

      php artisan palpalsi:make-facade {Facade name}

- Create simple Facade with check authorization:
        
        php artisan palpalsi:make-facade {Facade name} {option}

- All options are:
  - -a or --authorize
  - -c or --calling
  - -f or --full or -a && -c or --authorize && --calling
  - -h or --help

- If you use the Facade name without the 'Facade' word in a command, this will be appended to the end of Facade name

