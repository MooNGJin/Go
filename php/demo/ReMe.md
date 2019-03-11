
```
$consisHash = new ConsistentHash();
$consisHash->addServer("serv1")->addServer("serv2")->addServer("serv3");
$consisHash->printServiceList();
echo "key1 at " . $consisHash->find("kadsasd1") . ".\n";
echo "key2 at " . $consisHash->find("465s31d") . ".\n";
echo "key3 at " . $consisHash->find("ked3") . ".\n";


------------------
print----

Array
(
    [956317696] => serv1
    [222117888] => serv2
    [1896800256] => serv3
)
key1 at serv2.
key2 at serv2.
key3 at serv2.
```
