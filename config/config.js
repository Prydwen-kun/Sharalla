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
    getUserAdmin: 'user&action=getUserAdmin',
    getUserList: 'user&action=getUserList',
    updateUser: 'user&action=userUpdate',
    deleteUser: 'user&action=userDelete',
    logout: 'user&action=logout',
    files: {
      getFileList: 'file&action=getAllFiles',
      getFileData: 'file&action=getFile',
      getCurrentUserFiles: 'file&action=getConnectedUserFiles',
      updateFile: '',
      deleteFile: 'file&action=deleteFile',
      uploadFile: 'file&action=uploadFile',
      getEntriesNumber: 'file&action=getEntriesNumber',
      download: 'file&action=downloadFile',
      getComments: 'comment&action=getFileComment',
      sendComment: 'comment&action=sendComment',
    },
    GET: {
      userId: '&userId=',
      order: '&order=',
      l_size: '&l_size=',
      page: '&page=',
      search: '&search=',
      fileId: '&fileId=',
    },
  },
  AvatarBaseUrl: 'http://localhost/Sharalla/backend/',
  filter: {
    id: 'id',
    username: 'username',
    last_login: 'last_login',
    idDESC: 'idDESC',
    usernameDESC: 'usernameDESC',
    last_loginDESC: 'last_loginDESC',
  },
  f_filter: {
    id: 'id',
    title: 'title',
    size: 'size',
    idDESC: 'idDESC',
    titleDESC: 'titleDESC',
    sizeDESC: 'sizeDESC',
  },
}

export default config
