Installing Node
===============

You can't just do `apt-get install node` because that will install an old version. You can download it from the website or just run the following commands.

    sudo apt-get update
    sudo apt-get install -y python-software-properties python g++ make
    sudo add-apt-repository ppa:chris-lea/node.js
    sudo apt-get update
    sudo apt-get install nodejs

`node -v` should print something >= 0.10.24


Installing Dependencies
=======================

Pretty simple:

     sudo npm install


Running the Server
==================

This will open a server on `127.0.0.1:8000`:

    node app.js

Open a browser and connect to `http://127.0.0.1:8000/index.html`. This will open an editor. 

You can change the "channel" by adding a hash, e.g. `http://127.0.0.1:8000/index.html#room`. 

Separate browsers connected to the same channel should see all updates but different channels should not receive them.
