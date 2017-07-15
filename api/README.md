# MyECM - API RestFul

## Dev. Roadmap

* [x]  *v. alpha_01* - Implementation of Generic GET Services.
* [ ]  *v. alpha_02* - Implementation of Generic POST Services.
* [ ]  *v. alpha_03* - Implementation of Generic PUT/DELETE Services.

## Versions History

|Version # <br/>|Release date|Version comment <br/> |URI|
|:----------:|:---|:---:|:----:|
|*v. alpha_01*|2017-07-15|API Restful GET Services implemented. |toto|


### API - Entry Points

|endpoint|http_method|url_example|param1|param1type|param2|param2type|param3|param3type|param4|param4type|param5|param5type|SQLOrder|description|result_example|
|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|:----------:|
|fichier|GET|fichier/FIC-00000000002|uid|string|||||||||"SELECT id,uid,filename,filepath,filesize,mime,ctime,cuser,utime,uuser,json_data FROM tobj_fichiers WHERE uid = :param1"|Return a File metadata information.|
|categorie|GET|categorie/CAT-ADMIN|uid|string|||||||||"SELECT id,uid,code,title,description,ctime,cuser,utime,uuser,isActive,json_data FROM tref_categories WHERE  tref_categories.uid = :param1 "|Return all Categories.|
|tier|GET|tier/TIER-EDF|uid|string|||||||||"SELECT id,uid,code,title,description,ctime,cuser,utime,uuser,isActive,json_data FROM tref_tiers WHERE uid = :param1"|Return all Tiers.|
|typedoc|GET|typedoc/TDOC-FACT|uid|string|||||||||"SELECT id,uid,code,title,description,ctime,cuser,utime,uuser,isActive,json_data FROM tref_typesdoc WHERE uid = :param1"|Return all Type of Document.|
|metadata|GET|metadata/MDOC-0000000002|uid|string|||||||||"SELECT id,uid,code,typedoc_code,tdocmeta_code,doc_uid,title,value,json_data,ctime,cuser,utime,uuser,isActive FROM vobj_metadata_last WHERE uid = :param1"|Return a metadata instance from her uid.|
|document|GET|document/D-REMS-2016-003|code|string|||||||||"SELECT uid,code,version,revision,typedoc_code,ctnr_code,title,description,year,month,day,ctime,cuser,utime,uuser,isActive,json_data,meta_json,fic_json,cat_json,tier_json FROM vobj_documents_last WHERE code = :param1"|Return last |Version/Revision of a document from her code.|
|categorie|GET|categorie/|||||||||||"SELECT tref_categories.id,tref_categories.uid,tref_categories.code,tref_categories.title,tref_categories.description,tref_categories.ctime,tref_categories.cuser,tref_categories.utime,tref_categories.uuser,tref_categories.isActive,tref_categories.json_data FROM |tref_categories"|Return a Categorie informations from her uid.|
|typedoc|GET|typedoc/|||||||||||"SELECT id,uid,code,title,description,ctime,cuser,utime,uuser,isActive,json_data FROM tref_typesdoc"|Return all Type of Document |
|tier|GET|tier/|||||||||||"SELECT tref_tiers.id,tref_tiers.uid,tref_tiers.code,tref_tiers.title,tref_tiers.description,tref_tiers.ctime,tref_tiers.cuser,tref_tiers.utime,tref_tiers.uuser,tref_tiers.isActive,tref_tiers.json_data FROM tref_tiers"|Return all Tiers.|
|metadata|GET|metadata/|||||||||||"SELECT id,uid,code,typedoc_code,tdocmeta_code,doc_uid,title,value,json_data,ctime,cuser,utime,uuser,isActive FROM vobj_metadata_last"|Return all metadata.|
|document|GET|document/|||||||||||"SELECT uid,code,version,revision,typedoc_code,ctnr_code,title,description,year,month,day,ctime,cuser,utime,uuser,isActive,json_data,meta_json,fic_json,cat_json,tier_json FROM vobj_documents_last"|Return all documents.|
