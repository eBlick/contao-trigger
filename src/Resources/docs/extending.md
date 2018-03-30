Adding conditions & actions
---------------------------

Adding your own conditions and actions is pretty straightforward. Each
condition can fire multiple actions on evaluation. This allows to
effectively process large bits of data that should be checked against
the same condition (e.g. rows of a table).

Each condition can add custom data to the execution flow. To do so you
must specify how the data you're adding looks like via the function
`getDataProtoype()`. Note that only a flat structure is supported, yet.

If an action returns `true` a trigger log entry is made. Your condition
must check for the log entries and decide if actions should be fired
(again) or not. To do so you can use the functions inside the
`ExecutionContext` that is passed on evaluation. Each log entry is
associated with the trigger, an `origin` value (most likely the source
table or `tl_eblick_trigger` by default) and a `originId` to identify
individual records or iterations.

Throw an `ExecutionException` (or any other appropriate exception) if
you want or need to stop processing and give a reason. The trigger will
then be in an error mode until resolved in the backend and the exception
message will be shown.



#### Adding a custom Condition

 - Create a new service class that implements the
   `EBlick\ContaoTrigger\Component\Condition\ConditionInterface`.
   ```php
   <?php

   class MyCondition implements ConditionInterface {

       public function evaluate(
           ExecutionContext $context,
           Closure $fireCallback) : void {

           // here goes your logic

           $myCustomData = ['foo' => 10, 'bar' => 'abc']
           $fireCallback($myCustomData);
       }

       public function getDataPrototype(int $triggerId) : array {
           return ['foo' => null, 'bar' => null]
       }

   }

   // ...
   ```

 - See the interface's doc blocks for further information.
 - Tag your service:
   ```yml
   somewhere.custom.my_condition:
       class: SomeWhere\Custom\MyCondition
       tags:
         - { name: eblick_contao_trigger.condition, alias: mycondition }
   ```

 - Make sure to add a language file for your condition's name:
   ```php
   $GLOBALS['TL_LANG']['tl_eblick_trigger']['condition']['mycondition'] = ['My Condition'];
   ```



#### Adding a custom Action
 - Create a new service class that implements the
   `EBlick\ContaoTrigger\Component\Action\ActionInterface`.
   ```php
   <?php

   class MyAction implements ActionInterface {

       public function fire(
           ExecutionContext $context,
           array $rawData) : bool {

           // here goes your logic

           // action was executed
           return true;
       }
   }

   // ...
   ```

 - See the interface's doc blocks for further information.
 - Tag your service:
   ```yml
   somewhere.custom.my_action:
       class: SomeWhere\Custom\MyAction
       tags:
         - { name: eblick_contao_trigger.action, alias: myaction }
   ```

 - Make sure to add a language file for your condition's name:
   ```php
   $GLOBALS['TL_LANG']['tl_eblick_trigger']['action']['myaction'] = ['My Action'];
   ```


#### Adding dca fields

 - To add parameters and settings to your condition or action implement
   the `EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface`.

   ```php
   <?php

   class MyAction implements DataContainerComponentInterface, [...] {

      [...]

      public function getDataContainerDefinition(): Definition
      {
          $palette = 'act_something_value';

          $fields = [
              'act_something_value' =>
                  [
                      // ...
                      'inputType' => 'text',
                      'sql'       => "int(10) unsigned NOT NULL default '0'"
                  ]
          ];

          return new Definition($fields, $palette);
      }

   }

   // ...
   ```

 - In a definition you can specify fields, palettes, selectors and
   sub palettes like you would in a regular Contao dca container. This
   definition will than get merged as a subgroup into the
   `tl_eblick_trigger` dca and displayed as a selectable option. To avoid
   collisions it is suggested to prefix your fields with `act_myaction_`
   for actions and `cnd_mycondition_` for conditions.
