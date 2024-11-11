//general front constant to fill with backend endpoints
//brainrot beyond
const config = {
  APIbaseUrl:'http://localhost/Sharalla/backend/index.php?ctrl=',
  endpoints:{
    signup:'user&action=signup',
    login:'user&action=login',
    isConnected:'user&action=login',
    updateUser:'',
    deleteUser:'',

  }
}

export default config
