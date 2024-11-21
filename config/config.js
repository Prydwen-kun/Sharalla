//general front constant to fill with backend endpoints
//brainrot beyond
const config = {
  APIbaseUrl: 'http://localhost/Sharalla/backend/index.php?ctrl=',
  endpoints: {
    signup: 'user&action=signup',
    login: 'user&action=login',
    isConnected: 'user&action=isConnected',
    isUserConnected: 'user&action=isUserConnected',
    getConnectedUserData: 'user&action=getConnectedUserData',
    getUserData: 'user&action=getUserData',
    getUserList: 'user&action=getUserList',
    updateUser: 'user&action=userUpdate',
    deleteUser: 'user&action=userDelete',
    logout: 'user&action=logout',
    GET: {
      userId: '&userId=',
      order: '&order=',
      l_size: '&l_size=',
    },
  },
  filter: {
    id: 'id',
    username: 'username',
    last_login: 'last_login',
    idDESC: 'idDESC',
    usernameDESC: 'usernameDESC',
    last_loginDESC: 'last_loginDESC',
  },
}

export default config
