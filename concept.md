- version
- help
   
- suggest
    - parse composer.json's require-dev
    - list known tools and propose installation

- install
    - list from file "phive.xml" (recommended)

    - via url
        - https only
            - strict checking
            
    - via alias
        - lookup at phar.io or from phive.xml
        
    - gpg check
        - get key from keyserver
    - openssl check
    - shaX check
    
    - make executable
        - generate .bat for windows
        
    - set symlink / copy based on
        - phive.xml
        - command option
        - default: {dirname('/path/to/composer.json')}/bin

- remove 

- sync
    - compare current state with phive.xml definition and latest versions
        - act accordingly

- update 
    - in project
        - check for updates of known phars (from shared)
        - get new versions as needed (in shared)
        - update symlink / copy
        
   - in shared base   
        - check for updates of known phars
        - get new versions as needed

- show
    - dump database:
        - list known phars
        - projects using them
        - and their version
        
- clean
    - remove unused versions of phars
    - remove unused tools
    
- migrate
    - get all projects and set the tools to the latest
      version
      
- configure
    - ask a bunch of questions and generate phive.xml
        - storage directory
        - offer to parse composer.json
            - adopt list of tools from it
