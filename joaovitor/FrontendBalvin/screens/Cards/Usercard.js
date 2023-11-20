import { StyleSheet, Text, View, Image, TouchableOpacity } from 'react-native'
import React from 'react'

const Usercard = ({ user, navigation }) => {
  console.log(user)
  return (
      <TouchableOpacity onPress={
          () => {
              navigation.navigate('Outrosusuarios', { user: user })
          }
      }>
          <View style={styles.ChatCard}>
              {
                  user.profilepic ? <Image source={{ uri: user.profilepic }} style={styles.image} />
                      : <Image source={require('../../images/userpic.png')} style={styles.image} />
              }

              <View style={styles.c1}>
                  <Text style={styles.username}>{user.nome}</Text>
              </View>
          </View>
      </TouchableOpacity>
  )
}

export default Usercard

const styles = StyleSheet.create({

  ChatCard: {
    backgroundColor: 'white',
    width: '100%',
    marginTop: 0,
    padding: 10,
    flexDirection: 'row',
    alignItems: 'center',
},
image: {
    width: 40,
    height: 40,
    borderRadius: 50,
},
username: {
    color: 'black',
    fontSize: 20,
    fontWeight: 'bold',
},
c1: {
    marginLeft: 20,
},
lastmessage: {
    color: 'gray',
    fontSize: 19,
}

})