# MyECM - API RestFul

## Versions
### Version 01 - Entry Points

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|GET|/api/v1/document/|None|Returns an array containing all documents main attributes.|
|GET|/api/v1/document/{id}|{id} : Document UID|Returns data concerning aimed Document.|
|POST|/api/v1/document/|| Create a new document.|
|POST|/api/v1/document/{docid}/file/|{docid} : Document UID <br/>POST Arg : FileToUpload (Form)| Upload a file and link it to the document.|
|POST|/api/v1/document/{docid}/file/{fileid}|{docid} : Document UID <br/>{fileid} : File UID| Link an existing file to the document.|
|DELETE|/api/v1/document/{docid}/file/{fileid}|{docid} : Document UID <br/>{fileid} : File UID| Delete a link between file and document.|
