
//Config 
// const data_json_file_path = 'Mat_AnyOne_all';
const account_key_json_file_path = 'OpenPage-92aba2a64265';
const query_code = 'Mat_AnyOne_4A_S';
// const query_code = "Mat_AnyOne_5A_32_T"

//Import Library 
const admin = require('./node_modules/firebase-admin');
const serviceAccount = require(`./${account_key_json_file_path}.json`);

//Firestore initialization 
admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),  
    databaseURL: "https://development-170710.firebaseio.com",
  });

  //Get all 
  const db = admin.firestore();
  const pageRef = db.collection('pages');

  pageRef
    // .where('parentRef',"==",query_code)
    .where('book_instance_code',"==",query_code)
    .orderBy('page_number')
    .limit(70)
    .get().then(
        snapshot =>{
            // console.log(snapshot);
            snapshot.forEach(doc=>{
                console.log(doc.id,"=>", doc.data());
            })
        }
    )
    .catch(err=>{
        console.log('Error getting document', err);
    })