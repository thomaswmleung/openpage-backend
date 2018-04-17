
//Config 
// const data_json_file_path = 'Mat_AnyOne_all';
const account_key_json_file_path = 'OpenPage-92aba2a64265';
const addRecordFlag = true; 

//Import Library 
const admin = require('./node_modules/firebase-admin');
const serviceAccount = require(`./${account_key_json_file_path}.json`);
const convertExcel = require('excel-as-json').processFile;

//Firestore initialization 
admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),  
    databaseURL: "https://development-170710.firebaseio.com",
  });

// const data = require(`./${data_json_file_path}.json`);


/*
        src: path to source Excel file (xlsx only)
        dst: path to destination JSON file. If null, simply return the parsed object tree
        options: an object containing
            sheet: 1 based sheet index as text - default '1'
            isColOriented: are object values in columns with keys in column A - default false
            omitEmptyFields: omit empty Excel fields from JSON output - default false
        callback(err, data): callback for completion notification
*/

const fileKeyArray = ['MAT','Mat_Mock100_AT','Mat_AnyOne'];

fileKeyArray.forEach(key=>{
    convertExcel(
        `${key}.xlsx`, `${key}.json`,{},
        (err,data)=>{ //CallBack
            if(err){
             console.log(err, data);
            }else{
                if(data.forEach){
                    data.forEach(
                        item => {
                            const _collection_key = 'pages';
                            // console.log(item, _collection_key);
                            if(
                                item
                                && item.image_path
                                && addRecordFlag 
                            ){
                                admin.firestore()
                                .collection(_collection_key)
                                .add(item)
                                .then(
                                    ref=>console.log(`Added document with ID(${_collection_key}): `, ref.id)
                                );
                            }
                            
                        }
                    )
                }
            }
        }
    );
})



// if(data.forEach){
//     data.forEach(
//         item => {
//             // console.log(item);
//             const _collection_key = 'pages';
//             admin.firestore()
//                 .collection(_collection_key)
//                 .add(item)
//                 .then(
//                     ref=>console.log(`Added document with ID(${_collection_key}): `, ref.id)
//                 );
//         }
//     )
// }