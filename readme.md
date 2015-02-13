TypedCallable
==============================

Abstract PHP class, which allows to implement callables with signature check

Installation
============

Usage
============

Define your own class extending TypedCallable

    class OnDataCallable extends Czt08883\TypedCallable\TypedCallable\TypedCallable 
    {
        /**
         * Implement "useTemplate()" abstract method.
         * This method must return a callable, which will
         * be used as a template for signature check.
         *
         * @return callable
         */
        public function useTemplate()
        {
            return function(SomeClass $a, array $b, $c);
        }
    }    
    
    

In your other class, which requires typed callable:

  - store this typed callable as a property
  - invoke this typed callable on some event, passing some data 


     class DataReceiverExample 
     {
        /**
         * @var OnDataCallable
         */ 
        private $onDataCallback;
        
        /**
         * Use this typed callable as an argument in some function
         * to setup a callback
         */
        public function setOnDataCallback(OnDataCallable $callback)
        {
            $this->onDataCallback = $callback;
        }
        
        /**
         * Execute your callback on some event, passing proper parameters.
         * For example, on receiving data from external source
         */
         public function onData()
         {
            // ... receive some data and pass it to callback ...
            call_user_func(
                $this->onDataCallback, 
                [
                    new SomeClass, 
                    ['some','data','as','array'], 
                    "some string" 
                ]
            );
         }
     }
     
     
Use your DataReceiverExample
      
      $dataReceiver = new DataReceiverExample();
      $dataReceiver->setOnDataCallback(
          new OnDataCallable(
             function (SomeClass $a, array $b, $c) {
                // just display it, for example
                echo "Yay, recived some data.";
             }
          )
      );
      
      /* Simulate incoming data event:
       * This will invoke a callback, installed earlier. So output will be
       *     "Yay, recived some data."
       */
      $dataReceiver->onData();
      
      

Now let us test callback with wrong signature.
Following code will trigger TypedCallableSignatureMismatchException
with following message:
  "Callable signature mismatch. Callable must be: function(SomeClass $a, array $b, $c){...}"

       $dataReceiver->setOnDataCallback(
            new OnDataCallable(
               function (SomeOtherClass $c) {
                 // ... other actions, not important here
               }
            )
        );
      
      
     
     
